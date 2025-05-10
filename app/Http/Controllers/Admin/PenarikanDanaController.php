<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver\PenarikanDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PenarikanDanaController extends Controller
{
    // GET /api/admin/penarikan (List semua penarikan pending)
    public function index()
    {
        $penarikan = PenarikanDana::with(['driver'])
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $penarikan
        ]);
    }

    // PUT /api/admin/penarikan/{id}/approve (Approve/reject penarikan)
    public function update(Request $request, $id)
    {
        try {
            // Validasi manual untuk logging lebih detail
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:diproses,berhasil,gagal',
                'catatan' => 'required_if:status,gagal'
            ]);
    
            if ($validator->fails()) {
                Log::warning("Validasi gagal saat update penarikan ID {$id}: ", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validasi gagal'
                ], 422);
            }
    
            $penarikan = PenarikanDana::find($id);
    
            if (!$penarikan) {
                Log::warning("Data penarikan dengan ID {$id} tidak ditemukan.");
                return response()->json([
                    'success' => false,
                    'message' => 'Data penarikan tidak ditemukan'
                ], 404);
            }
    
            // Pastikan relasi driver ada
            if (!$penarikan->driver) {
                Log::error("Driver tidak ditemukan untuk penarikan ID {$id}.");
                return response()->json([
                    'success' => false,
                    'message' => 'Driver tidak ditemukan untuk penarikan ini'
                ], 500);
            }
    
            // Jika ditolak, kembalikan saldo ke driver
            if ($request->status === 'gagal') {
                $penarikan->driver->increment('saldo_penghasilan', $penarikan->jumlah);
                Log::info("Saldo sebesar {$penarikan->jumlah} dikembalikan ke driver ID {$penarikan->driver_id} karena status 'gagal'.");
            }
    
            $penarikan->update([
                'status' => $request->status,
                'catatan' => $request->catatan
            ]);
    
            Log::info("Penarikan ID {$penarikan->id} berhasil diperbarui ke status '{$request->status}'.");
    
            return response()->json([
                'success' => true,
                'data' => $penarikan
            ]);
        } catch (\Exception $e) {
            Log::error("Terjadi error saat update penarikan ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
}