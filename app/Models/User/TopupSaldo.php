<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopupSaldo extends Model
{
    protected $connection = 'mysql_user';
    protected $table = 'TOPUP_SALDO';
    protected $primaryKey = 'topup_id';

    protected $fillable = [
        'user_id',
        'jumlah',
        'metode',
        'status',
        'referensi_pembayaran',
        'bukti_pembayaran'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'status' => 'string' // 'pending', 'berhasil', 'gagal'
    ];

    // Relasi ke penumpang
    public function penumpang(): BelongsTo
    {
        return $this->belongsTo(Penumpang::class, 'user_id');
    }

    // Scope untuk topup pending
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}