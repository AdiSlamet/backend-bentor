<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\TopupSaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TopupSaldoController extends Controller
{
    // GET /api/user/topup (Histori topup)
    public function index(Request $request)
    {
        $topups = TopupSaldo::where('user_id', $request->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topups
        ]);
    }

    // POST /api/user/topup (Buat topup baru)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:10000',
            'metode' => 'required|in:bank_transfer,e-wallet',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload bukti pembayaran
        $buktiPath = $request->file('bukti_pembayaran')->store('topup', 'public');

        $topup = TopupSaldo::create([
            'user_id' => $request->user()->user_id,
            'jumlah' => $request->jumlah,
            'metode' => $request->metode,
            'status' => 'pending',
            'bukti_pembayaran' => $buktiPath,
            'referensi_pembayaran' => 'TOP-' . time() . '-' . strtoupper(uniqid())
        ]);

        return response()->json([
            'success' => true,
            'data' => $topup
        ], 201);
    }

    // GET /api/user/topup/{id} (Detail topup)
    public function show(Request $request, $id)
    {
        $topup = TopupSaldo::where('topup_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if (!$topup) {
            return response()->json([
                'success' => false,
                'message' => 'Topup tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $topup
        ]);
    }
}