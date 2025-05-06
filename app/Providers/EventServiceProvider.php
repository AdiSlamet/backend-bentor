<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\NotifikasiDibuat;
use App\Listeners\KirimNotifikasiDriver;
use App\Http\Controllers\User;
use App\Models\User\Penumpang;
use App\Models\Driver\Driver;
use App\Models\Shared\Notifikasi;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Daftar event dan listener yang dipetakan.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \App\Events\PerjalananDibuat::class => [
            \App\Listeners\KirimNotifikasiDriver::class,
        ],

        \App\Events\PembayaranBerhasil::class => [
            \App\Listeners\KirimNotifikasiPembayaran::class,
        ],
        
        // Tambahkan event lainnya di sini bila perlu
        // Contoh:
        // \App\Events\UserRegistered::class => [
        //     \App\Listeners\SendWelcomeEmail::class,
        // ],
    ];

    /**
     * Daftar event yang tidak menggunakan auto-discovery.
     *
     * @var array
     */
    protected $dontDiscoverEvents = [
        //
    ];

    /**
     * Mendaftarkan event untuk aplikasi.
     */
    public function boot(): void
    {
        parent::boot();

        // Tambahkan event binding manual di sini jika diperlukan
        // Event::listen(...);
    }
    // public function handle(NotifikasiDibuat $event): void
    // {
    //     $notifikasi = $event->notifikasi;
    //     $target = $notifikasi->user_id ? Penumpang::find($notifikasi->user_id) : Driver::find($notifikasi->driver_id);

    //     if ($target && $target->fcm_token) {
    //         FCM::sendTo($target->fcm_token, [
    //             'title' => $notifikasi->judul,
    //             'body' => $notifikasi->pesan,
    //             'data' => [
    //                 'notifikasi_id' => $notifikasi->notifikasi_id,
    //                 'tipe' => $notifikasi->tipe
    //             ]
    //         ]);
    //     }
    // }
}
