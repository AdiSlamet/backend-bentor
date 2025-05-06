<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\Rating;
use App\Models\Shared\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    // GET /api/rating (List rating)
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Rating::query()
            ->with(['perjalanan', 'penumpang', 'driver'])
            ->orderBy('created_at', 'desc');

        if ($user->tokenCan('driver')) {
            $query->where('driver_id', $user->driver_id);
        } else {
            $query->where('user_id', $user->user_id);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    // POST /api/rating (Buat rating baru)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perjalanan_id' => 'required|exists:mysql_admin.PERJALANAN,perjalanan_id',
            'nilai_rating' => 'required|integer|between:1,5',
            'deskripsi_review' => 'nullable|string|max:500',
            'tipe' => 'required|in:user_to_driver,driver_to_user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $perjalanan = Perjalanan::findOrFail($request->perjalanan_id);

        // Validasi kepemilikan perjalanan
        $user = $request->user();
        if ($user->tokenCan('driver')) {
            if ($perjalanan->driver_id !== $user->driver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        } else {
            if ($perjalanan->user_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // Validasi apakah perjalanan sudah selesai
        if ($perjalanan->status !== 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Perjalanan belum selesai'
            ], 400);
        }

        // Validasi apakah sudah memberikan rating untuk tipe ini
        $existingRating = Rating::where('perjalanan_id', $request->perjalanan_id)
            ->where('tipe', $request->tipe)
            ->exists();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan rating untuk tipe ini'
            ], 400);
        }

        $rating = Rating::create([
            'perjalanan_id' => $perjalanan->perjalanan_id,
            'user_id' => $perjalanan->user_id,
            'driver_id' => $perjalanan->driver_id,
            'nilai_rating' => $request->nilai_rating,
            'deskripsi_review' => $request->deskripsi_review,
            'tipe' => $request->tipe
        ]);

        // Update rating rata-rata
        $this->updateAverageRating($rating);

        return response()->json([
            'success' => true,
            'data' => $rating
        ], 201);
    }

    // GET /api/rating/{id} (Detail rating)
    public function show(Request $request, $id)
    {
        $rating = Rating::with(['perjalanan', 'penumpang', 'driver'])
            ->findOrFail($id);

        // Validasi kepemilikan
        $user = $request->user();
        if ($user->tokenCan('driver')) {
            if ($rating->driver_id !== $user->driver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        } else {
            if ($rating->user_id !== $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $rating
        ]);
    }

    // Helper untuk update rating rata-rata
    private function updateAverageRating(Rating $rating): void
    {
        if ($rating->tipe === 'user_to_driver') {
            $driver = $rating->driver;
            $average = Rating::untukDriver()
                ->where('driver_id', $driver->driver_id)
                ->avg('nilai_rating');
            
            $driver->update([
                'rating_rata_rata' => round($average, 2),
                'jumlah_perjalanan' => $driver->jumlah_perjalanan + 1
            ]);
        } else {
            $penumpang = $rating->penumpang;
            $average = Rating::untukUser()
                ->where('user_id', $penumpang->user_id)
                ->avg('nilai_rating');
            
            // Jika ingin menyimpan rating user juga
            // $penumpang->update(['rating_rata_rata' => round($average, 2)]);
        }
    }
}