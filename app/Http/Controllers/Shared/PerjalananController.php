<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Admin\Promosi as AdminPromosi;
use App\Models\Admin\Tarif as AdminTarif;
use App\Models\Shared\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Shared\Tarif;
use App\Models\Shared\Promosi;

class PerjalananController extends Controller
{
    // GET /api/perjalanan (List perjalanan user/driver)
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Perjalanan::query();
        
        if ($user->tokenCan('driver')) {
            $query->where('driver_id', $user->driver_id);
        } else {
            $query->where('user_id', $user->user_id);
        }
        
        $perjalanan = $query->orderBy('waktu_pesan', 'desc')
            ->with(['penumpang', 'driver', 'pembayaran'])
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $perjalanan
        ]);
    }

    // POST /api/perjalanan (Buat perjalanan baru - penumpang)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lokasi_jemput_lat' => 'required|numeric',
            'lokasi_jemput_long' => 'required|numeric',
            'alamat_jemput' => 'required|string',
            'lokasi_tujuan_lat' => 'required|numeric',
            'lokasi_tujuan_long' => 'required|numeric',
            'alamat_tujuan' => 'required|string',
            'jarak_km' => 'required|numeric|min:0.1',
            'promo_code' => 'sometimes|string|exists:mysql_admin.PROMOSI,kode_promo'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Hitung harga
        $tarif = AdminTarif::where('status_aktif', true)->firstOrFail();
        $hargaDasar = max(
            $tarif->tarif_minimum,
            $tarif->tarif_dasar + ($request->jarak_km * $tarif->tarif_per_km)
        );

        $diskon = 0;
        $promosiId = null;

        // Cek promo jika ada
        if ($request->promo_code) {
            $promosi = AdminPromosi::where('kode_promo', $request->promo_code)
                ->where('status_aktif', true)
                ->where('berlaku_mulai', '<=', now())
                ->where('berlaku_sampai', '>=', now())
                ->first();

            if ($promosi) {
                $diskon = $promosi->tipe_diskon === 'persentase' 
                    ? ($hargaDasar * $promosi->nilai_diskon / 100)
                    : $promosi->nilai_diskon;
                $promosiId = $promosi->promosi_id;
            }
        }

        $perjalanan = Perjalanan::create([
            'user_id' => $request->user()->user_id,
            'lokasi_jemput_lat' => $request->lokasi_jemput_lat,
            'lokasi_jemput_long' => $request->lokasi_jemput_long,
            'alamat_jemput' => $request->alamat_jemput,
            'lokasi_tujuan_lat' => $request->lokasi_tujuan_lat,
            'lokasi_tujuan_long' => $request->lokasi_tujuan_long,
            'alamat_tujuan' => $request->alamat_tujuan,
            'jarak_km' => $request->jarak_km,
            'durasi_estimasi_menit' => $this->hitungDurasi($request),
            'harga_dasar' => $hargaDasar,
            'biaya_platform' => $tarif->biaya_platform,
            'diskon' => $diskon,
            'promosi_id' => $promosiId,
            'harga_final' => $hargaDasar + $tarif->biaya_platform - $diskon,
            'waktu_pesan' => now(),
            'status' => 'mencari_driver'
        ]);

        return response()->json([
            'success' => true,
            'data' => $perjalanan
        ], 201);
    }

    // GET /api/perjalanan/{id} (Detail perjalanan)
    public function show(Request $request, $id)
    {
        $perjalanan = Perjalanan::with([
                'penumpang', 
                'driver', 
                'pembayaran',
                'tracking'
            ])
            ->find($id);

        // Cek authorization
        $user = $request->user();
        if ($user->tokenCan('driver')) {
            if ($perjalanan->driver_id !== $user->driver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        } else {
            if ($perjalanan->user_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $perjalanan
        ]);
    }

    // PUT /api/perjalanan/{id}/status (Update status perjalanan)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:driver_ditemukan,menuju_penjemputan,dalam_perjalanan,selesai,dibatalkan',
            'alasan_batal' => 'required_if:status,dibatalkan'
        ]);

        $perjalanan = Perjalanan::findOrFail($id);

        // Validasi perubahan status
        $allowedTransitions = [
            'mencari_driver' => ['driver_ditemukan', 'dibatalkan'],
            'driver_ditemukan' => ['menuju_penjemputan', 'dibatalkan'],
            'menuju_penjemputan' => ['dalam_perjalanan', 'dibatalkan'],
            'dalam_perjalanan' => ['selesai', 'dibatalkan']
        ];

        if (!in_array($request->status, $allowedTransitions[$perjalanan->status] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Transisi status tidak valid'
            ], 400);
        }

        // Jika status driver_ditemukan, pastikan driver_id dikirim
        if ($request->status === 'driver_ditemukan') {
            $request->validate(['driver_id' => 'required|exists:mysql_driver.DRIVER,driver_id']);
            $perjalanan->driver_id = $request->driver_id;
        }

        // Jika status selesai, set waktu_selesai
        if ($request->status === 'selesai') {
            $perjalanan->waktu_selesai = now();
        }

        $perjalanan->status = $request->status;
        $perjalanan->alasan_batal = $request->alasan_batal;
        $perjalanan->save();

        return response()->json([
            'success' => true,
            'data' => $perjalanan
        ]);
    }

    // Helper untuk menghitung durasi estimasi
    private function hitungDurasi(Request $request): int
    {
        // Implementasi sebenarnya bisa menggunakan API seperti Google Maps
        // Ini hanya contoh sederhana
        return (int) ($request->jarak_km * 2); // Asumsi 2 menit per km
    }
}