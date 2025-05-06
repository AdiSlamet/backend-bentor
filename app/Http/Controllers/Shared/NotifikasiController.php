<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\NotifikasiDibuat;


class NotifikasiController extends Controller
{
    // GET /api/notifikasi (List notifikasi)
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Notifikasi::query()
            ->orderBy('created_at', 'desc')
            ->limit(50);

        if ($user->tokenCan('driver')) {
            $query->forDriver($user->driver_id);
        } else {
            $query->forUser($user->user_id);
        }

        // Filter berdasarkan tipe
        if ($request->has('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter belum dibaca
        if ($request->boolean('unread')) {
            $query->unread();
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    // POST /api/notifikasi (Buat notifikasi)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required_without:driver_id|exists:mysql_user.PENUMPANG,user_id',
            'driver_id' => 'required_without:user_id|exists:mysql_driver.DRIVER,driver_id',
            'judul' => 'required|string|max:100',
            'pesan' => 'required|string',
            'tipe' => 'required|in:order,system,promo',
            'notifiable_id' => 'sometimes|integer',
            'notifiable_type' => 'required_with:notifiable_id|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $notifikasi = Notifikasi::create([
            'user_id' => $request->user_id,
            'driver_id' => $request->driver_id,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'tipe' => $request->tipe,
            'dibaca' => false,
            'notifiable_id' => $request->notifiable_id,
            'notifiable_type' => $request->notifiable_type
        ]);

        // Kirim push notification
        // event(new NotifikasiDibuat($notifikasi));

        return response()->json([
            'success' => true,
            'data' => $notifikasi
        ], 201);
    }

    // PUT /api/notifikasi/{id}/read (Tandai sudah dibaca)
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notifikasi = Notifikasi::where('notifikasi_id', $id)
            ->when($user->tokenCan('driver'), function($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            }, function($q) use ($user) {
                $q->where('user_id', $user->user_id);
            })
            ->firstOrFail();

        $notifikasi->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'data' => $notifikasi
        ]);
    }

    // PUT /api/notifikasi/read-all (Tandai semua sudah dibaca)
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $query = Notifikasi::query()
            ->unread()
            ->when($user->tokenCan('driver'), function($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            }, function($q) use ($user) {
                $q->where('user_id', $user->user_id);
            });

        $count = $query->count();
        $query->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'message' => "{$count} notifikasi ditandai sebagai sudah dibaca"
        ]);
    }

    // GET /api/notifikasi/unread-count (Jumlah notifikasi belum dibaca)
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = Notifikasi::query()
            ->unread()
            ->when($user->tokenCan('driver'), function($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            }, function($q) use ($user) {
                $q->where('user_id', $user->user_id);
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }
}