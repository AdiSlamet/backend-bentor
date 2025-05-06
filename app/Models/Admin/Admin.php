<?php

namespace App\Models\Admin;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Koneksi database khusus admin
    protected $connection = 'mysql_admin';
    protected $table = 'ADMIN';
    protected $primaryKey = 'admin_id';
    // $incrementing = true;

    // Field yang bisa diisi
    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_telepon'
    ];

    // Field yang disembunyikan di response
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke tabel TIKET_BANTUAN (contoh)
    public function tiketBantuans()
    {
        return $this->hasMany(TiketBantuan::class, 'admin_id');
    }
}