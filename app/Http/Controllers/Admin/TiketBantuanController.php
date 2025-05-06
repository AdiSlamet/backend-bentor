<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TiketBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiketBantuanController extends Controller
{
    // GET /api/admin/tiket-bantuan
    public function index()
    {
        $tikets = TiketBantuan::with(['admin', 'responTikets'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $tikets
        ]);
    }

    // POST /api/admin/tiket-bantuan
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjek' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'user_id' => 'nullable|integer',   // ID penumpang (jika ada)
            'driver_id' => 'nullable|integer'  // ID driver (jika ada)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tiket = TiketBantuan::create([
            'user_id' => $request->user_id,
            'driver_id' => $request->driver_id,
            'subjek' => $request->subjek,
            'deskripsi' => $request->deskripsi,
            'status' => 'open'
        ]);

        return response()->json([
            'success' => true,
            'data' => $tiket
        ], 201);
    }

    // GET /api/admin/tiket-bantuan/{id}
    public function show($id)
    {
        $tiket = TiketBantuan::with(['admin', 'responTikets'])->find($id);
        
        if (!$tiket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tiket
        ]);
    }

    // PUT /api/admin/tiket-bantuan/{id}
    public function update(Request $request, $id)
    {
        $tiket = TiketBantuan::find($id);
        
        if (!$tiket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:open,in-progress,closed',
            'admin_id' => 'sometimes|exists:mysql_admin.ADMIN,admin_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tiket->update([
            'status' => $request->status ?? $tiket->status,
            'admin_id' => $request->admin_id ?? $tiket->admin_id
        ]);

        return response()->json([
            'success' => true,
            'data' => $tiket
        ]);
    }
}