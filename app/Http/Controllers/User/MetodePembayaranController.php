<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MetodePembayaranController extends Controller
{
    // GET /api/user/metode-pembayaran (List metode pembayaran user)
    public function index(Request $request)
    {
        $metodes = MetodePembayaran::where('user_id', $request->user()->user_id)
            ->orderBy('utama', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $metodes
        ]);
    }

    // POST /api/user/metode-pembayaran (Tambah metode baru)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe' => 'required|in:e-wallet,kartu',
            'provider' => 'required|string',
            'nomor_akun' => 'required|string',
            'utama' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Jika di-set sebagai utama, nonaktifkan yang lain
        if ($request->utama) {
            MetodePembayaran::where('user_id', $request->user()->user_id)
                ->update(['utama' => false]);
        }

        $metode = MetodePembayaran::create([
            'user_id' => $request->user()->user_id,
            'tipe' => $request->tipe,
            'provider' => $request->provider,
            'nomor_akun' => $request->nomor_akun,
            'status' => 'aktif',
            'utama' => $request->utama ?? false
        ]);

        return response()->json([
            'success' => true,
            'data' => $metode
        ], 201);
    }

    // PUT /api/user/metode-pembayaran/{id} (Update metode)
    public function update(Request $request, $id)
    {
        $metode = MetodePembayaran::where('metode_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if (!$metode) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'provider' => 'sometimes|string',
            'nomor_akun' => 'sometimes|string',
            'status' => 'sometimes|in:aktif,nonaktif',
            'utama' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Jika di-set sebagai utama, nonaktifkan yang lain
        if ($request->has('utama') && $request->utama) {
            MetodePembayaran::where('user_id', $request->user()->user_id)
                ->where('metode_id', '!=', $id)
                ->update(['utama' => false]);
        }

        $metode->update($request->only([
            'provider',
            'nomor_akun',
            'status',
            'utama'
        ]));

        return response()->json([
            'success' => true,
            'data' => $metode
        ]);
    }

    // DELETE /api/user/metode-pembayaran/{id} (Hapus metode)
    public function destroy($id)
    {
        $metode = MetodePembayaran::where('metode_id', $id)
            ->where('user_id', request()->user()->user_id)
            ->first();

        if (!$metode) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tidak ditemukan'
            ], 404);
        }

        // Jika metode utama dihapus, set metode lain sebagai utama
        if ($metode->utama) {
            MetodePembayaran::where('user_id', request()->user()->user_id)
                ->where('metode_id', '!=', $id)
                ->first()
                ?->update(['utama' => true]);
        }

        $metode->delete();
        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran dihapus'
        ]);
    }
}