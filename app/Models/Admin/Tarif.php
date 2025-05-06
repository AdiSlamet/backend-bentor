<?php

namespace App\Models\Admin;

use App\Models\Shared\Perjalanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'TARIF';
    protected $primaryKey = 'tarif_id';

    protected $fillable = [
        'tarif_dasar',
        'tarif_per_km',
        'tarif_minimum',
        'biaya_platform',
        'berlaku_sejak',
        'status_aktif',
    ];

    protected $casts = [
        'tarif_dasar' => 'decimal:2',
        'tarif_per_km' => 'decimal:2',
        'tarif_minimum' => 'decimal:2',
        'biaya_platform' => 'decimal:2',
        'berlaku_sejak' => 'datetime'
    ];

    // Relasi ke perjalanan
    public function perjalanan(): HasMany
    {
        return $this->hasMany(Perjalanan::class, 'tarif_id');
    }
}