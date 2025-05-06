<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlamatFavorit extends Model
{
    protected $connection = 'mysql_user';
    protected $table = 'ALAMAT_FAVORIT';
    protected $primaryKey = 'alamat_id';

    protected $fillable = [
        'user_id',
        'nama_alamat',
        'alamat_lengkap',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    // Relasi ke penumpang
    public function penumpang(): BelongsTo
    {
        return $this->belongsTo(Penumpang::class, 'user_id');
    }

    // Accessor untuk alamat lengkap
    public function getAlamatLengkapAttribute($value)
    {
        return ucfirst($value);
    }
}