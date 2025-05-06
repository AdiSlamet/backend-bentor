<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Promosi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromosiController extends Controller
{
    // GET /api/admin/promosi
    public function index(Request $request)
    {
        $query = Promosi::query();
        
        // Filter status aktif
        if ($request->has('status')) {
            $query->where('status_aktif', $request->status);
        }
        
        // Filter promo yang sedang berjalan
        if ($request->has('sedang_berjalan')) {
            $query->where('berlaku_mulai', '<=', now())
                 ->where('berlaku_sampai', '>=', now());
        }
        
        $promos = $query->orderBy('berlaku_mulai', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $promos
        ]);
    }

    // POST /api/admin/promosi
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'nilai_diskon' => 'required|numeric|min:0',
            'tipe_diskon' => 'required|in:persentase,nominal',
            'kode_promo' => 'required|string|unique:mysql_admin.PROMOSI,kode_promo',
            'berlaku_mulai' => 'required|date',
            'berlaku_sampai' => 'required|date|after:berlaku_mulai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $promo = Promosi::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'nilai_diskon' => $request->nilai_diskon,
            'tipe_diskon' => $request->tipe_diskon,
            'kode_promo' => $request->kode_promo,
            'berlaku_mulai' => $request->berlaku_mulai,
            'berlaku_sampai' => $request->berlaku_sampai,
            'status_aktif' => $request->status_aktif ?? true
        ]);

        return response()->json([
            'success' => true,
            'data' => $promo
        ], 201);
    }

    // GET /api/admin/promosi/aktif
    public function aktif()
    {
        $promos = Promosi::aktif()->get();
        
        return response()->json([
            'success' => true,
            'data' => $promos
        ]);
    }

    // GET /api/admin/promosi/{id}
    public function show($id)
    {
        $promo = Promosi::find($id);
        
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promosi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $promo
        ]);
    }

    // PUT /api/admin/promosi/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_aktif' => 'required|boolean'
        ]);

        $promo = Promosi::findOrFail($id);
        $promo->update(['status_aktif' => $request->status_aktif]);

        return response()->json([
            'success' => true,
            'message' => 'Status promosi berhasil diupdate'
        ]);
    }

    // DELETE /api/admin/promosi/{id}
    public function destroy($id)
    {
        $promo = Promosi::find($id);
        
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promosi tidak ditemukan'
            ], 404);
        }

        // Cek apakah promo pernah digunakan
        if ($promo->perjalanan()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus promosi yang pernah digunakan'
            ], 400);
        }

        $promo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Promosi berhasil dihapus'
        ]);
    }
}