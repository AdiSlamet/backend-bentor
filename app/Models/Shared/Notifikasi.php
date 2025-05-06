<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notifikasi extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'NOTIFIKASI';
    protected $primaryKey = 'notifikasi_id';

    protected $fillable = [
        'user_id',
        'driver_id',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
        'notifiable_id',
        'notifiable_type'
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'tipe' => 'string' // 'order', 'system', 'promo'
    ];

    // Relasi polymorphic untuk berbagai tipe notifikasi
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scope untuk notifikasi belum dibaca
    public function scopeUnread($query)
    {
        return $query->where('dibaca', false);
    }

    // Scope untuk notifikasi user/driver
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}