<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User\Penumpang;
use App\Models\Driver\Driver;
use App\Models\Admin\Promosi;
use App\Models\Admin\Pembayaran;
use App\Models\Driver\TrackingPerjalanan;


class Perjalanan extends Model
{
    protected $connection = 'mysql_admin'; // Menggunakan koneksi admin
    protected $table = 'PERJALANAN';
    protected $primaryKey = 'perjalanan_id';

    protected $fillable = [
        'user_id',
        'driver_id',
        'lokasi_jemput_lat',
        'lokasi_jemput_long',
        'alamat_jemput',
        'lokasi_tujuan_lat',
        'lokasi_tujuan_long',
        'alamat_tujuan',
        'jarak_km',
        'durasi_estimasi_menit',
        'harga_dasar',
        'biaya_platform',
        'diskon',
        'promosi_id',
        'harga_final',
        'waktu_pesan',
        'waktu_jemput',
        'waktu_selesai',
        'status',
        'alasan_batal'
    ];

    protected $casts = [
        'lokasi_jemput_lat' => 'decimal:8',
        'lokasi_jemput_long' => 'decimal:8',
        'lokasi_tujuan_lat' => 'decimal:8',
        'lokasi_tujuan_long' => 'decimal:8',
        'jarak_km' => 'decimal:2',
        'harga_dasar' => 'decimal:2',
        'biaya_platform' => 'decimal:2',
        'diskon' => 'decimal:2',
        'harga_final' => 'decimal:2',
        'waktu_pesan' => 'datetime',
        'waktu_jemput' => 'datetime',
        'waktu_selesai' => 'datetime'
    ];

    // Relasi ke penumpang
    public function penumpang(): BelongsTo
    {
        return $this->belongsTo(Penumpang::class, 'user_id');
    }

    // Relasi ke driver
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // Relasi ke promosi
    public function promosi(): BelongsTo
    {
        return $this->belongsTo(Promosi::class, 'promosi_id');
    }

    // Relasi ke pembayaran
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'perjalanan_id');
    }

    // Relasi ke tracking
    public function tracking(): HasMany
    {
        return $this->hasMany(TrackingPerjalanan::class, 'perjalanan_id');
    }

    // Scope untuk perjalanan aktif
    public function scopeAktif($query)
    {
        return $query->whereIn('status', [
            'mencari_driver',
            'driver_ditemukan',
            'menuju_penjemputan',
            'dalam_perjalanan'
        ]);
    }
}