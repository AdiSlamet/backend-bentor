<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $connection = 'mysql_driver';
    protected $table = 'KENDARAAN';
    protected $primaryKey = 'kendaraan_id';

    protected $fillable = [
        'driver_id',
        'nomor_polisi',
        'nomor_rangka',
        'merk',
        'model',
        'warna',
        'tahun_produksi',
        'foto_kendaraan'
    ];

    // Relasi ke driver
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}