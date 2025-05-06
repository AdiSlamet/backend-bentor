<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver\PenarikanDana;
use Illuminate\Http\Request;

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
        $request->validate([
            'status' => 'required|in:diproses,berhasil,gagal',
            'catatan' => 'required_if:status,gagal'
        ]);

        $penarikan = PenarikanDana::find($id);
        
        if (!$penarikan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penarikan tidak ditemukan'
            ], 404);
        }

        // Jika ditolak, kembalikan saldo ke driver
        if ($request->status === 'gagal') {
            $penarikan->driver->increment('saldo_penghasilan', $penarikan->jumlah);
        }

        $penarikan->update([
            'status' => $request->status,
            'catatan' => $request->catatan
        ]);

        return response()->json([
            'success' => true,
            'data' => $penarikan
        ]);
    }
}