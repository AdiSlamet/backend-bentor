<?php

namespace App\Models\Driver;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenVerifikasi extends Model
{
    protected $connection = 'mysql_driver';
    protected $table = 'DOKUMEN_DRIVER';
    protected $primaryKey = 'dokumen_id';

    protected $fillable = [
        'driver_id',
        'jenis_dokumen',
        'nomor_dokumen',
        'file_dokumen',
        'status_verifikasi',
        'catatan_verifikasi',
        'admin_id'
    ];

    protected $casts = [
        'status_verifikasi' => 'string' // 'pending', 'valid', 'invalid'
    ];

    // Relasi ke driver
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'driver_id');
    }

    // Relasi ke admin yang memverifikasi
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Accessor untuk URL file dokumen
    public function getFileDokumenUrlAttribute()
    {
        return $this->file_dokumen ? asset('storage/' . $this->file_dokumen) : null;
    }
}