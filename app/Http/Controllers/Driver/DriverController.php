<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    // GET /api/driver/drivers
    public function index()
    {
        $drivers = Driver::with(['kendaraan', 'dokumen'])->get();
        return response()->json([
            'success' => true,
            'data' => $drivers
        ]);
    }

    // POST /api/driver/drivers (Register)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:mysql_driver.DRIVER,email',
            'no_telepon' => 'required',
            'password' => 'required|min:8',
            'nomor_ktp' => 'required|unique:mysql_driver.DRIVER,nomor_ktp'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = Driver::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'password' => Hash::make($request->password),
            'nomor_ktp' => $request->nomor_ktp,
            'status_aktif' => 'offline'
        ]);

        return response()->json([
            'success' => true,
            'data' => $driver
        ], 201);
    }

    // GET /api/driver/drivers/{id}
    public function show($id)
    {
        $driver = Driver::with(['kendaraan', 'dokumen'])->find($id);
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $driver
        ]);
    }

    // PUT /api/driver/drivers/{id}
    public function update(Request $request, $id)
    {
        $driver = Driver::find($id);
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:mysql_driver.DRIVER,email,'.$id.',driver_id',
            'no_telepon' => 'sometimes',
            'password' => 'sometimes|min:8',
            'status_aktif' => 'sometimes|in:online,offline'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'nama_lengkap',
            'email',
            'no_telepon',
            'status_aktif',
            'latitude_terakhir',
            'longitude_terakhir'
        ]);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $driver->update($data);

        return response()->json([
            'success' => true,
            'data' => $driver
        ]);
    }

    // DELETE /api/driver/drivers/{id} (Hanya admin yang bisa akses)
    public function destroy($id)
    {
        $driver = Driver::find($id);
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver tidak ditemukan'
            ], 404);
        }

        $driver->delete();
        return response()->json([
            'success' => true,
            'message' => 'Driver berhasil dihapus'
        ], 204);
    }
}