<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\DokumenDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DokumenDriverController extends Controller
{
    // GET /api/driver/dokumen (List dokumen driver)
    public function index(Request $request)
    {
        $dokumen = DokumenDriver::where('driver_id', $request->user()->driver_id)
            ->with(['admin'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    // POST /api/driver/dokumen (Upload dokumen baru)
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            Log::info('Autentikasi berhasil untuk unggah dokumen', [
                'driver_id' => $user->driver_id ?? null,
                'email' => $user->email ?? 'tidak diketahui',
            ]);

            $validator = Validator::make($request->all(), [
                'jenis_dokumen' => 'required|in:KTP,SIM,STNK,SKCK',
                'nomor_dokumen' => 'required|string',
                'file_dokumen' => 'required|file|mimes:pdf,jpg,png|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi dokumen gagal', [
                    'driver_id' => $user->driver_id ?? null,
                    'errors' => $validator->errors()
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $filePath = $request->file('file_dokumen')->store('dokumen_driver');

            $dokumen = DokumenDriver::create([
                'driver_id' => $user->driver_id,
                'jenis_dokumen' => $request->jenis_dokumen,
                'nomor_dokumen' => $request->nomor_dokumen,
                'file_dokumen' => $filePath,
                'status_verifikasi' => 'pending'
            ]);

            Log::info('Dokumen berhasil disimpan', [
                'dokumen_id' => $dokumen->id,
                'driver_id' => $user->driver_id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $dokumen
            ], 201);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan dokumen driver', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'driver_id' => $request->user()->driver_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan dokumen.'
            ], 500);
        }
    }

    // GET /api/driver/dokumen/{id} (Detail dokumen)
    public function show($id)
    {
        $dokumen = DokumenDriver::where('dokumen_id', $id)
            ->where('driver_id', request()->user()->driver_id)
            ->first();

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    // DELETE /api/driver/dokumen/{id} (Hapus dokumen)
    public function destroy($id)
    {
        $dokumen = DokumenDriver::where('dokumen_id', $id)
            ->where('driver_id', request()->user()->driver_id)
            ->first();

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }

        // Hapus file dari storage
        Storage::delete($dokumen->file_dokumen);

        $dokumen->delete();
        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus'
        ]);
    }
}