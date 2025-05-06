<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenarikanDana extends Model
{
    protected $connection = 'mysql_driver';
    protected $table = 'PENARIKAN_DANA';
    protected $primaryKey = 'penarikan_id';

    protected $fillable = [
        'driver_id',
        'jumlah',
        'bank_tujuan',
        'nomor_rekening',
        'nama_pemilik',
        'status',
        'catatan'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'status' => 'string' // 'pending', 'diproses', 'berhasil', 'gagal'
    ];

    // Relasi ke driver
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}