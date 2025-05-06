<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\Pembayaran;
use App\Models\Shared\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    // GET /api/pembayaran (Histori pembayaran user/driver)
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Pembayaran::query()
            ->with(['perjalanan', 'metodePembayaran'])
            ->orderBy('waktu_pembayaran', 'desc');

        if ($user->tokenCan('driver')) {
            $query->whereHas('perjalanan', function($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            });
        } else {
            $query->whereHas('perjalanan', function($q) use ($user) {
                $q->where('user_id', $user->user_id);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    // POST /api/pembayaran (Buat pembayaran - penumpang)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perjalanan_id' => 'required|exists:mysql_admin.PERJALANAN,perjalanan_id',
            'metode' => 'required|in:cash,e-wallet',
            'metode_pembayaran_id' => 'required_if:metode,e-wallet|exists:mysql_user.METODE_PEMBAYARAN,metode_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $perjalanan = Perjalanan::findOrFail($request->perjalanan_id);

        // Validasi kepemilikan perjalanan
        if ($perjalanan->user_id !== $request->user()->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Validasi status perjalanan
        if ($perjalanan->status !== 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Perjalanan belum selesai'
            ], 400);
        }

        // Cek apakah sudah ada pembayaran
        if ($perjalanan->pembayaran()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran untuk perjalanan ini sudah ada'
            ], 400);
        }

        $pembayaran = Pembayaran::create([
            'perjalanan_id' => $perjalanan->perjalanan_id,
            'metode' => $request->metode,
            'metode_pembayaran_id' => $request->metode_pembayaran_id,
            'jumlah' => $perjalanan->harga_final,
            'status' => $request->metode === 'cash' ? 'berhasil' : 'pending',
            'referensi_pembayaran' => $request->metode === 'cash' 
                ? 'CASH-' . time() 
                : 'EW-' . strtoupper(uniqid()),
            'waktu_pembayaran' => $request->metode === 'cash' ? now() : null
        ]);

        // Jika cash, langsung update status perjalanan
        if ($request->metode === 'cash') {
            $perjalanan->update(['status_pembayaran' => 'lunas']);
        }

        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ], 201);
    }

    // GET /api/pembayaran/{id} (Detail pembayaran)
    public function show(Request $request, $id)
    {
        $pembayaran = Pembayaran::with(['perjalanan', 'metodePembayaran'])
            ->findOrFail($id);

        // Validasi kepemilikan
        $user = $request->user();
        if ($user->tokenCan('driver')) {
            if ($pembayaran->perjalanan->driver_id !== $user->driver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        } else {
            if ($pembayaran->perjalanan->user_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ]);
    }

    // POST /api/pembayaran/{id}/verify (Verifikasi pembayaran e-wallet)
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:berhasil,gagal'
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        // Hanya untuk e-wallet yang pending
        if ($pembayaran->metode !== 'e-wallet' || $pembayaran->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pembayaran e-wallet yang pending dapat diverifikasi'
            ], 400);
        }

        $pembayaran->update([
            'status' => $request->status,
            'waktu_pembayaran' => $request->status === 'berhasil' ? now() : null
        ]);

        // Jika berhasil, update saldo dan status perjalanan
        if ($request->status === 'berhasil') {
            $pembayaran->perjalanan->update(['status_pembayaran' => 'lunas']);
            
            // Potong saldo penumpang jika menggunakan e-wallet
            if ($pembayaran->metode_pembayaran_id) {
                $penumpang = $pembayaran->perjalanan->penumpang;
                $penumpang->decrement('saldo_ewallet', $pembayaran->jumlah);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ]);
    }
}