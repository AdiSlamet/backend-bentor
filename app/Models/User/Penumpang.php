<?php

namespace App\Models\User;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Penumpang extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'mysql_user';
    protected $table = 'PENUMPANG';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_telepon',
        'password',
        'foto_profil',
        'verified',
        'saldo_ewallet'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'saldo_ewallet' => 'decimal:2'
    ];

    // Relasi ke alamat favorit
    public function alamatFavorit()
    {
        return $this->hasMany(AlamatFavorit::class, 'user_id');
    }

    // Relasi ke metode pembayaran
    public function metodePembayaran()
    {
        return $this->hasMany(MetodePembayaran::class, 'user_id');
    }
}