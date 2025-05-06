<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KendaraanController extends Controller
{
    // GET /api/driver/kendaraan (List kendaraan driver)
    public function index(Request $request)
    {
        $kendaraan = Kendaraan::where('driver_id', $request->user()->driver_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kendaraan
        ]);
    }

    // POST /api/driver/kendaraan (Tambah kendaraan)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_polisi' => 'required|string|unique:mysql_driver.KENDARAAN,nomor_polisi',
            'nomor_rangka' => 'required|string|unique:mysql_driver.KENDARAAN,nomor_rangka',
            'merk' => 'required|string',
            'model' => 'required|string',
            'warna' => 'required|string',
            'tahun_produksi' => 'required|integer|min:1990|max:'.(date('Y')+1),
            'foto_kendaraan' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload foto kendaraan
        $fotoPath = $request->file('foto_kendaraan')->store('kendaraan', 'public');

        $kendaraan = Kendaraan::create([
            'driver_id' => $request->user()->driver_id,
            'nomor_polisi' => $request->nomor_polisi,
            'nomor_rangka' => $request->nomor_rangka,
            'merk' => $request->merk,
            'model' => $request->model,
            'warna' => $request->warna,
            'tahun_produksi' => $request->tahun_produksi,
            'foto_kendaraan' => $fotoPath
        ]);

        return response()->json([
            'success' => true,
            'data' => $kendaraan
        ], 201);
    }

    // GET /api/driver/kendaraan/{id}
    public function show(Request $request, $id)
    {
        $kendaraan = Kendaraan::where('kendaraan_id', $id)
            ->where('driver_id', $request->user()->driver_id)
            ->first();

        if (!$kendaraan) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kendaraan
        ]);
    }

    // PUT /api/driver/kendaraan/{id}
    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::where('kendaraan_id', $id)
            ->where('driver_id', $request->user()->driver_id)
            ->first();

        if (!$kendaraan) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomor_polisi' => 'sometimes|string|unique:mysql_driver.KENDARAAN,nomor_polisi,'.$id.',kendaraan_id',
            'nomor_rangka' => 'sometimes|string|unique:mysql_driver.KENDARAAN,nomor_rangka,'.$id.',kendaraan_id',
            'merk' => 'sometimes|string',
            'model' => 'sometimes|string',
            'warna' => 'sometimes|string',
            'tahun_produksi' => 'sometimes|integer|min:1990|max:'.(date('Y')+1),
            'foto_kendaraan' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'nomor_polisi',
            'nomor_rangka',
            'merk',
            'model',
            'warna',
            'tahun_produksi'
        ]);

        // Update foto jika ada
        if ($request->hasFile('foto_kendaraan')) {
            // Hapus foto lama
            Storage::disk('public')->delete($kendaraan->foto_kendaraan);
            
            // Upload foto baru
            $data['foto_kendaraan'] = $request->file('foto_kendaraan')->store('kendaraan', 'public');
        }

        $kendaraan->update($data);

        return response()->json([
            'success' => true,
            'data' => $kendaraan
        ]);
    }

    // DELETE /api/driver/kendaraan/{id}
    public function destroy(Request $request, $id)
    {
        $kendaraan = Kendaraan::where('kendaraan_id', $id)
            ->where('driver_id', $request->user()->driver_id)
            ->first();

        if (!$kendaraan) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan tidak ditemukan'
            ], 404);
        }

        // Hapus foto dari storage
        Storage::disk('public')->delete($kendaraan->foto_kendaraan);

        $kendaraan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kendaraan berhasil dihapus'
        ]);
    }
}