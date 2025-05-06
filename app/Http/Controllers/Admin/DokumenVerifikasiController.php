<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver\DokumenDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokumenVerifikasiController extends Controller
{
    // GET /api/admin/dokumen (List semua dokumen pending)
    public function index()
    {
        $dokumen = DokumenDriver::with(['driver'])
            ->where('status_verifikasi', 'pending')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    // PUT /api/admin/dokumen/{id}/verify (Verifikasi dokumen)


    public function verify(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'status' => 'required|in:valid,invalid',
                'catatan' => 'required_if:status,invalid'
            ]);
    
            // Cari dokumen
            $dokumen = DokumenDriver::find($id);
    
            if (!$dokumen) {
                Log::warning("Verifikasi gagal: dokumen dengan ID $id tidak ditemukan.");
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }
    
            // Update data
            $dokumen->update([
                'status_verifikasi' => $request->status,
                'catatan_verifikasi' => $request->catatan,
                'admin_id' => $request->user()->admin_id
                // 'driver_id' => $request->user()->driver_id
            ]);
    
            Log::info("Dokumen ID $id berhasil diverifikasi dengan status {$request->status}.");

            return response()->json([
                'success' => true,
                'data' => $dokumen
            ]);
    
        } catch (\Exception $e) {
            // Tangani error tak terduga
            Log::error("Terjadi error saat verifikasi dokumen ID $id: " . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi dokumen.',
                'error' => $e->getMessage() // Bisa dihapus di production
            ], 500);
        }
    }    
}