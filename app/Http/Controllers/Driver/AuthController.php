<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/driver/login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $driver = Driver::where('email', $request->email)->first();

        if (!$driver || !Hash::check($request->password, $driver->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $driver->createToken('driver-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'data' => $driver
        ]);
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