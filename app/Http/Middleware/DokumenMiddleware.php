<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class DokumenMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan user sudah login
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Please login first'
            ], 401);
        }

        // 2. Verifikasi role admin untuk route verify
        if ($request->isMethod('put') && $request->route()->named('verify')) {
            if (!auth()->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden - Only admin can verify documents'
                ], 403);
            }
        }

        // 3. Untuk route lainnya, pastikan user adalah pemilik dokumen atau admin
        $dokumenId = $request->route('id');
        if ($dokumenId && !auth()->user()->is_admin) {
            $isOwner = \App\Models\Driver\DokumenDriver::where('id', $dokumenId)
                        ->where('user_id', auth()->id())
                        ->exists();
            
            if (!$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden - You can only access your own documents'
                ], 403);
            }
        }

        return $next($request);
    }
}