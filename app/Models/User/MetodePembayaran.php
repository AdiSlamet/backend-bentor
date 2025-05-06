<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetodePembayaran extends Model
{
    protected $connection = 'mysql_user';
    protected $table = 'METODE_PEMBAYARAN';
    protected $primaryKey = 'metode_id';

    protected $fillable = [
        'user_id',
        'tipe',
        'provider',
        'nomor_akun',
        'status',
        'utama'
    ];

    protected $casts = [
        'utama' => 'boolean',
        'status' => 'string' // 'aktif', 'nonaktif'
    ];

    // Relasi ke penumpang
    public function penumpang(): BelongsTo
    {
        return $this->belongsTo(Penumpang::class, 'user_id');
    }
}