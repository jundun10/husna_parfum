<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alamat extends Model
{
    protected $fillable = [
    'user_id',
    'nama_penerima',
    'no_hp',
    'provinsi',
    'kabupaten_kota',
    'kecamatan',
    'desa',
    'alamat_lengkap',
    'kode_pos',
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function pesanans()
{
    return $this->hasMany(Pesanan::class);
}
}