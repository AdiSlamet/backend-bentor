<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    // POST /api/admin/login
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     $admin = Admin::where('email', $request->email)->first();

    //     if (!$admin || !Hash::check($request->password, $admin->password)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Email atau password salah'
    //         ], 401);
    //     }

    //     // Generate token Sanctum
    //     $token = $admin->createToken('admin-token')->plainTextToken;

    //     return response()->json([
    //         'success' => true,
    //         'token' => $token,
    //         'data' => $admin
    //     ]);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Hapus token lama (opsional)
        $admin->tokens()->delete();

        // Generate token baru dengan nama yang spesifik
        $token = $admin->createToken('admin-auth-token', ['admin'])->plainTextToken;
        
        // Log token yang dibuat untuk debugging
        \Log::info('Admin login successful', [
            'admin_id' => $admin->admin_id,
            'token_generated' => $token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token_type' => 'Bearer',
            'token' => $token,
            'data' => $admin
        ]);
    }

    // POST /api/admin/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    // GET /api/admin/me (Profil admin yang login)
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }
}