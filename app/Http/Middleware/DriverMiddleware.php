<?php

namespace App\Http\Middleware;

use App\Models\Driver\Driver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\PersonalAccessToken;

class DriverMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['message' => 'Token required'], 401);
        }

        try {
            // 1. Cari token HANYA di koneksi driver
            $token = PersonalAccessToken::on('mysql_driver')
                ->where('token', hash('sha256', $bearerToken))
                ->first();

            // 2. Verifikasi token milik driver
            if (!$token || !$this->isDriverToken($token)) {
                Log::warning('Invalid driver token attempt', [
                    'token_exists' => (bool)$token,
                    'tokenable_type' => $token?->tokenable_type,
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'message' => 'Invalid driver token',
                    'hint' => 'Please login using driver credentials'
                ], 403);
            }

            // 3. Dapatkan dan verifikasi driver
            $driver = $token->tokenable;
            if (!$driver) {
                return response()->json(['message' => 'Driver account not found'], 404);
            }

            // 4. Set autentikasi
            Auth::shouldUse('driver');
            Auth::setUser($driver);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Driver auth error: '.$e->getMessage());
            return response()->json(['message' => 'Authentication error'], 500);
        }
    }

    /**
     * Verifikasi token milik driver
     */
    protected function isDriverToken($token): bool
    {
        $driverTypes = [
            'App\Models\Driver',
            'App\Models\Driver\Driver',  // Sesuaikan dengan namespace Anda
            'Driver' // Alternatif lain
        ];

        return in_array($token->tokenable_type, $driverTypes);
    }
}