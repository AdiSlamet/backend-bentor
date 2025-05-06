<?php

namespace App\Listeners;

use App\Events\PembayaranBerhasil;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KirimNotifikasiPembayaran
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PembayaranBerhasil $event): void
    {
        //
    }
}
