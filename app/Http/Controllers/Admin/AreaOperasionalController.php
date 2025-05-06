<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AreaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaOperasionalController extends Controller
{
    // GET /api/admin/area-operasional
    public function index()
    {
        $areas = AreaOperasional::all();
        
        return response()->json([
            'success' => true,
            'data' => $areas
        ]);
    }

    // POST /api/admin/area-operasional
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $area = AreaOperasional::create([
            'nama_area' => $request->nama_area,
            'deskripsi' => $request->deskripsi,
            'status_aktif' => $request->status_aktif ?? true
        ]);

        return response()->json([
            'success' => true,
            'data' => $area
        ], 201);
    }

    // GET /api/admin/area-operasional/{id}
    public function show($id)
    {
        $area = AreaOperasional::find($id);
        
        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area operasional tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $area
        ]);
    }

    // PUT /api/admin/area-operasional/{id}
    public function update(Request $request, $id)
    {
        $area = AreaOperasional::find($id);
        
        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area operasional tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_area' => 'sometimes|string|max:255',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $area->update([
            'nama_area' => $request->nama_area ?? $area->nama_area,
            'deskripsi' => $request->deskripsi ?? $area->deskripsi,
            'status_aktif' => $request->has('status_aktif') 
                ? $request->status_aktif 
                : $area->status_aktif
        ]);

        return response()->json([
            'success' => true,
            'data' => $area
        ]);
    }

    // DELETE /api/admin/area-operasional/{id}
    public function destroy($id)
    {
        $area = AreaOperasional::find($id);
        
        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area operasional tidak ditemukan'
            ], 404);
        }

        // Cek relasi sebelum hapus (optional)
        if ($area->perjalanan()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus area yang memiliki riwayat perjalanan'
            ], 400);
        }

        $area->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Area operasional berhasil dihapus'
        ]);
    }
}