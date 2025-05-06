<?php

namespace App\Models\Admin;

use App\Models\User\Penumpang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TiketBantuan extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'TIKET_BANTUAN';
    protected $primaryKey = 'tiket_id';

    protected $fillable = [
        'user_id',
        'driver_id',
        'subjek',
        'deskripsi',
        'status',
        'admin_id'
    ];

    protected $casts = [
        'status' => 'string' // 'open', 'in-progress', 'closed'
    ];

    // Relasi ke Admin yang menangani tiket
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Relasi ke respon tiket
    public function responTikets(): HasMany
    {
        return $this->hasMany(TiketRespon::class, 'tiket_id');
    }

    // Contoh: Akses relasi ke PENUMPANG (jika perlu)
    // Note: Karena PENUMPANG ada di database berbeda, ini hanya contoh konsep
    public function penumpang()
    {
        // Asumsi menggunakan cross-database relation (tidak didukung langsung oleh Laravel)
        // Solusi alternatif: Query manual atau service terpisah
        return $this->belongsTo(Penumpang::class, 'user_id')
                   ->setConnection('mysql_user');
    }
}