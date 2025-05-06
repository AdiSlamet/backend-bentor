<?php

namespace App\Models\Admin;

use App\Models\Shared\Perjalanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promosi extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'PROMOSI';
    protected $primaryKey = 'promosi_id';

    protected $fillable = [
        'judul',
        'deskripsi',
        'nilai_diskon',
        'tipe_diskon',
        'kode_promo',
        'berlaku_mulai',
        'berlaku_sampai',
        'status_aktif'
    ];

    protected $casts = [
        'nilai_diskon' => 'decimal:2',
        'berlaku_mulai' => 'datetime',
        'berlaku_sampai' => 'datetime',
        'status_aktif' => 'boolean'
    ];

    // Relasi ke perjalanan yang menggunakan promo
    public function perjalanan(): HasMany
    {
        return $this->hasMany(Perjalanan::class, 'promosi_id');
    }

    // Scope untuk promo aktif
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true)
                   ->where('berlaku_mulai', '<=', now())
                   ->where('berlaku_sampai', '>=', now());
    }
}