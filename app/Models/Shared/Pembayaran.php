<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'PEMBAYARAN';
    protected $primaryKey = 'pembayaran_id';

    protected $fillable = [
        'perjalanan_id',
        'metode',
        'metode_pembayaran_id',
        'jumlah',
        'status',
        'referensi_pembayaran',
        'waktu_pembayaran'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'waktu_pembayaran' => 'datetime',
        'status' => 'string' // 'pending', 'berhasil', 'gagal'
    ];

    // Relasi ke perjalanan
    public function perjalanan(): BelongsTo
    {
        return $this->belongsTo(Perjalanan::class, 'perjalanan_id');
    }

    // Relasi ke metode pembayaran (jika menggunakan e-wallet/kartu)
    public function metodePembayaran(): BelongsTo
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id');
    }

    // Scope untuk pembayaran berhasil
    public function scopeBerhasil($query)
    {
        return $query->where('status', 'berhasil');
    }
}