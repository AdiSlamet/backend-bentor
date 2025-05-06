<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\TrackingPerjalanan;
use App\Models\Shared\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackingPerjalananController extends Controller
{
    // GET /api/tracking/{perjalanan_id} (Get semua tracking perjalanan)
    public function index($perjalanan_id, Request $request)
    {
        $perjalanan = Perjalanan::findOrFail($perjalanan_id);

        // Authorization check
        $user = $request->user();
        if ($user->tokenCan('driver')) {
            if ($perjalanan->driver_id !== $user->driver_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($perjalanan->user_id !== $user->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $trackings = TrackingPerjalanan::where('perjalanan_id', $perjalanan_id)
            ->orderBy('waktu', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trackings
        ]);
    }

    // POST /api/tracking (Create tracking point)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perjalanan_id' => 'required|exists:mysql_admin.PERJALANAN,perjalanan_id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $perjalanan = Perjalanan::find($request->perjalanan_id);

        // Hanya driver yang terkait yang bisa update tracking
        if ($request->user()->driver_id !== $perjalanan->driver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $tracking = TrackingPerjalanan::create([
            'perjalanan_id' => $request->perjalanan_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'waktu' => now()
        ]);

        // Update lokasi terakhir driver
        $perjalanan->driver->update([
            'latitude_terakhir' => $request->latitude,
            'longitude_terakhir' => $request->longitude,
            'last_online' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $tracking
        ], 201);
    }

    // GET /api/tracking/{perjalanan_id}/latest (Get latest tracking)
    public function latest($perjalanan_id, Request $request)
    {
        $perjalanan = Perjalanan::findOrFail($perjalanan_id);

        // Authorization check
        $user = $request->user();
        if ($user->tokenCan('driver')) {
            if ($perjalanan->driver_id !== $user->driver_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($perjalanan->user_id !== $user->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $tracking = TrackingPerjalanan::where('perjalanan_id', $perjalanan_id)
            ->latest('waktu')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $tracking
        ]);
    }
}