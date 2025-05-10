<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // POST /api/driver/login
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     $driver = Driver::where('email', $request->email)->first();

    //     if (!$driver || !Hash::check($request->password, $driver->password)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Email atau password salah'
    //         ], 401);
    //     }

    //     // Hapus semua token sebelumnya
    //     $driver->tokens()->delete();

    //     // Buat token baru
    //     $token = $driver->createToken('driver-token', ['role:driver'])->plainTextToken;

    //     return response()->json([
    //         'success' => true,
    //         'token' => $token,
    //         'data' => $driver
    //     ]);
    // }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6'
        ]);

        try {
            // Gunakan koneksi mysql_driver secara eksplisit
            $driver = Driver::on('mysql_driver')
                ->where('email', $request->email)
                ->first();

            // Verifikasi driver dan password
            // if (!$driver || !Hash::check($request->password, $driver->password)) {
            //     Log::warning('Login attempt failed', [
            //         'email' => $request->email,
            //         'ip' => $request->ip()
            //     ]);
                
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Email atau password salah'
            //     ], 401);
            // }

            // // Cek jika driver sudah diverifikasi
            // if ($driver->status_verifikasi !== 'valid') {
            //     Log::warning('Unverified driver attempt', [
            //         'driver_id' => $driver->driver_id,
            //         'status' => $driver->status_verifikasi
            //     ]);
                
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Akun belum diverifikasi. Silakan hubungi admin.'
            //     ], 403);
            // }

            // Hapus semua token sebelumnya
            $driver->tokens()->delete();

            // Buat token baru dengan abilities
            $token = $driver->createToken('driver-auth-token', [
                'driver:access',
                'driver:id:'.$driver->driver_id
            ])->plainTextToken;

            // Update last login
            $driver->update([
                'last_online' => now(),
                'status_aktif' => 'online'
            ]);

            Log::info('Driver logged in successfully', [
                'driver_id' => $driver->driver_id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('sanctum.expiration'), // Jika menggunakan expiry
                'data' => $driver->only([
                    'driver_id',
                    'nama_lengkap',
                    'email',
                    'no_telepon',
                    'foto_profil',
                    'rating_rata_rata',
                    'status_aktif'
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Login error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    // POST /api/driver/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    // GET /api/driver/me (Profil driver yang login)
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('kendaraan')
        ]);
    }
}