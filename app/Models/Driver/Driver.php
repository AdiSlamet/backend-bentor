<?php

namespace App\Models\Driver;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\PersonalAccessToken;

class Driver extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'mysql_driver';
    protected $table = 'DRIVER';
    protected $primaryKey = 'driver_id';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_telepon',
        'password',
        'foto_profil',
        'nomor_ktp',
        'rating_rata_rata',
        'status_aktif',
        'saldo_penghasilan',
        'latitude_terakhir',
        'longitude_terakhir'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nomor_ktp'
    ];

    protected $casts = [
        'status_aktif' => 'string', // 'online' atau 'offline'
        'rating_rata_rata' => 'float'
    ];

    // Relasi ke kendaraan
    public function kendaraan()
    {
        return $this->hasOne(Kendaraan::class, 'driver_id');
    }

    // Relasi ke dokumen
    public function dokumen()
    {
        return $this->hasMany(DokumenDriver::class, 'driver_id');
    }

    // Relasi ke penarikan dana
    public function penarikanDana()
    {
        return $this->hasMany(PenarikanDana::class, 'driver_id');
    }
public function tokens()
{
    // Solusi 1: Gunakan model khusus yang sudah dikonfigurasi koneksinya
    return $this->morphMany(PersonalAccessToken::class, 'tokenable');
}

// Tambahkan method ini untuk override createToken
// public function createToken(string $name, array $abilities = ['*'], DateTimeInterface $expiresAt = null)
// {
//     // Simpan token di database utama
//     config(['database.default' => 'mysql']); // Ganti dengan connection default Anda
    
//     $token = parent::createToken($name, $abilities, $expiresAt);
    
//     // Kembalikan ke connection semula
//     config(['database.default' => 'mysql_driver']);
    
//     return $token;
// }

}