<?php

namespace App\Models\Admin;

use App\Models\Shared\Perjalanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaOperasional extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'AREA_OPERASIONAL';
    protected $primaryKey = 'area_id';

    protected $fillable = [
        'nama_area',
        'deskripsi',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    // Relasi ke perjalanan (jika diperlukan)
    public function perjalanan(): HasMany
    {
        return $this->hasMany(Perjalanan::class, 'area_id');
    }
}