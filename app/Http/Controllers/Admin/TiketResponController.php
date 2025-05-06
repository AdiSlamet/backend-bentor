<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TiketRespon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiketResponController extends Controller
{
    // POST /api/admin/tiket-bantuan/{tiket_id}/respon
    public function store(Request $request, $tiket_id)
    {
        $request->validate([
            'pesan' => 'required|string'
        ]);

        $respon = TiketRespon::create([
            'tiket_id' => $tiket_id,
            'admin_id' => Auth::id(), // Admin yang sedang login
            'pesan' => $request->pesan
        ]);

        return response()->json([
            'success' => true,
            'data' => $respon
        ], 201);
    }

    // GET /api/admin/tiket-bantuan/{tiket_id}/respon
    public function index($tiket_id)
    {
        $respon = TiketRespon::where('tiket_id', $tiket_id)
            ->with('admin')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $respon
        ]);
    }
}