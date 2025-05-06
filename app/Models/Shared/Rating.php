<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Admin\Driver;
use App\Models\Admin\Penumpang;
use App\Models\Driver\Driver as DriverDriver;
use Illuminate\Contracts\Concurrency\Driver as ConcurrencyDriver;
use SebastianBergmann\CodeCoverage\Driver\Driver as CodeCoverageDriverDriver;

class Rating extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'RATING';
    protected $primaryKey = 'rating_id';

    protected $fillable = [
        'perjalanan_id',
        'user_id',
        'driver_id',
        'nilai_rating',
        'deskripsi_review',
        'tipe'
    ];

    protected $casts = [
        'nilai_rating' => 'integer',
        'tipe' => 'string' // 'user_to_driver', 'driver_to_user'
    ];

    // Relasi ke perjalanan
    public function perjalanan(): BelongsTo
    {
        return $this->belongsTo(Perjalanan::class, 'perjalanan_id');
    }

    // Relasi ke penumpang
    public function penumpang(): BelongsTo
    {
        return $this->belongsTo(Penumpang::class, 'user_id', 'user_id');
    }

    // Relasi ke driver
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // Scope untuk rating driver
    public function scopeUntukDriver($query)
    {
        return $query->where('tipe', 'user_to_driver');
    }

    // Scope untuk rating user
    public function scopeUntukUser($query)
    {
        return $query->where('tipe', 'driver_to_user');
    }
}