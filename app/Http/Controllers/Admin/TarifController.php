<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TarifController extends Controller
{
    // GET /api/admin/tarif
    public function index()
    {
        $tarifs = Tarif::orderBy('berlaku_sejak', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tarifs
        ]);
    }

    // POST /api/admin/tarif
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tarif_dasar' => 'required|numeric|min:0',
            'tarif_per_km' => 'required|numeric|min:0',
            'tarif_minimum' => 'required|numeric|min:0',
            'biaya_platform' => 'required|numeric|min:0',
            'berlaku_sejak' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Nonaktifkan tarif sebelumnya
        Tarif::query()->update(['status_aktif' => false]);

        $tarif = Tarif::create([
            'tarif_dasar' => $request->tarif_dasar,
            'tarif_per_km' => $request->tarif_per_km,
            'tarif_minimum' => $request->tarif_minimum,
            'biaya_platform' => $request->biaya_platform,
            'berlaku_sejak' => $request->berlaku_sejak,
            'status_aktif' => true
        ]);

        return response()->json([
            'success' => true,
            'data' => $tarif
        ], 201);
    }

    // GET /api/admin/tarif/aktif
    public function showAktif()
    {
        $tarif = Tarif::where('status_aktif', true)->first();
        
        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tarif aktif'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tarif
        ]);
    }

    // GET /api/admin/tarif/{id}
    public function show($id)
    {
        $tarif = Tarif::find($id);
        
        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tarif
        ]);
    }

    // PUT /api/admin/tarif/{id}/aktifkan
    public function activate($id)
    {
        DB::transaction(function () use ($id) {
            // Nonaktifkan semua tarif
            Tarif::query()->update(['status_aktif' => false]);
            
            // Aktifkan tarif yang dipilih
            $tarif = Tarif::findOrFail($id);
            $tarif->update([
                'status_aktif' => true,
                'berlaku_sejak' => now()
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil diaktifkan'
        ]);
    }
}