<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketRespon extends Model
{
    protected $connection = 'mysql_admin';
    protected $table = 'TIKET_RESPON';
    protected $primaryKey = 'respon_id';

    protected $fillable = [
        'tiket_id',
        'admin_id',
        'pesan'
    ];

    // Relasi ke tiket
    public function tiket(): BelongsTo
    {
        return $this->belongsTo(TiketBantuan::class, 'tiket_id');
    }

    // Relasi ke admin yang merespon
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}