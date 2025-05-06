<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\AlamatFavorit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlamatFavoritController extends Controller
{
    // GET /api/user/alamat-favorit (List alamat favorit)
    public function index(Request $request)
    {
        $alamatFavorit = AlamatFavorit::where('user_id', $request->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $alamatFavorit
        ]);
    }

    // POST /api/user/alamat-favorit (Tambah alamat favorit)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_alamat' => 'required|string|max:100',
            'alamat_lengkap' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $alamat = AlamatFavorit::create([
            'user_id' => $request->user()->user_id,
            'nama_alamat' => $request->nama_alamat,
            'alamat_lengkap' => $request->alamat_lengkap,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return response()->json([
            'success' => true,
            'data' => $alamat
        ], 201);
    }

    // GET /api/user/alamat-favorit/{id} (Detail alamat)
    public function show(Request $request, $id)
    {
        $alamat = AlamatFavorit::where('alamat_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $alamat
        ]);
    }

    // PUT /api/user/alamat-favorit/{id} (Update alamat)
    public function update(Request $request, $id)
    {
        $alamat = AlamatFavorit::where('alamat_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_alamat' => 'sometimes|string|max:100',
            'alamat_lengkap' => 'sometimes|string',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $alamat->update($request->only([
            'nama_alamat',
            'alamat_lengkap',
            'latitude',
            'longitude'
        ]));

        return response()->json([
            'success' => true,
            'data' => $alamat
        ]);
    }

    // DELETE /api/user/alamat-favorit/{id} (Hapus alamat)
    public function destroy(Request $request, $id)
    {
        $alamat = AlamatFavorit::where('alamat_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        $alamat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat favorit berhasil dihapus'
        ]);
    }
}