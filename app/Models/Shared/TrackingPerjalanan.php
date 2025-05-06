<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingPerjalanan extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'TRACKING_PERJALANAN';
    protected $primaryKey = 'tracking_id';

    protected $fillable = [
        'perjalanan_id',
        'latitude',
        'longitude',
        'waktu'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'waktu' => 'datetime'
    ];

    // Relasi ke perjalanan
    public function perjalanan(): BelongsTo
    {
        return $this->belongsTo(Perjalanan::class, 'perjalanan_id');
    }

    // Accessor untuk format waktu
    public function getWaktuAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
}