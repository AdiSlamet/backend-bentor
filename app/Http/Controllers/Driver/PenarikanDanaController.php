<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\PenarikanDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenarikanDanaController extends Controller
{
    // GET /api/driver/penarikan (Histori penarikan)
    public function index(Request $request)
    {
        $penarikan = PenarikanDana::where('driver_id', $request->user()->driver_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $penarikan
        ]);
    }

    // POST /api/driver/penarikan (Ajukan penarikan baru)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:50000',
            'bank_tujuan' => 'required|string',
            'nomor_rekening' => 'required|string',
            'nama_pemilik' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek saldo mencukupi
        $driver = $request->user();
        if ($driver->saldo_penghasilan < $request->jumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        $penarikan = PenarikanDana::create([
            'driver_id' => $driver->driver_id,
            'jumlah' => $request->jumlah,
            'bank_tujuan' => $request->bank_tujuan,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_pemilik' => $request->nama_pemilik,
            'status' => 'pending'
        ]);

        // Update saldo driver (dikurangi)
        $driver->decrement('saldo_penghasilan', $request->jumlah);

        return response()->json([
            'success' => true,
            'data' => $penarikan
        ], 201);
    }

    // GET /api/driver/penarikan/{id} (Detail penarikan)
    public function show($id)
    {
        $penarikan = PenarikanDana::where('penarikan_id', $id)
            ->where('driver_id', request()->user()->driver_id)
            ->first();

        if (!$penarikan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penarikan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $penarikan
        ]);
    }
}