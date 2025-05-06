<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();
        
        if (!$bearerToken) {
            return response()->json(['message' => 'Token missing'], 401);
        }

        // Debug 1: Verify raw token
        \Log::info('Token verification:', [
            'raw_token' => $bearerToken,
            'length' => strlen($bearerToken),
            'first_10_chars' => substr($bearerToken, 0, 10)
        ]);

        // Debug 2: Alternative token lookup
        $token = PersonalAccessToken::whereRaw(
            "token = SHA2(?, 256)", 
            [$bearerToken]
        )->first();

        \Log::info('Database token lookup:', [
            'exists' => (bool)$token,
            'tokenable_type' => $token?->tokenable_type,
            'tokenable_id' => $token?->tokenable_id
        ]);

        if (!$token || $token->tokenable_type !== 'App\Models\Admin\Admin') {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        Auth::shouldUse('admin');
        Auth::guard('admin')->setUser($token->tokenable);

        return $next($request);
    }
}