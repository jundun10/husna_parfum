<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananItem extends Model
{
    protected $fillable = [
        'pesanan_id',
        'parfum_id',
        'jumlah',
        'harga',
        'subtotal',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function parfum(): BelongsTo
    {
        return $this->belongsTo(Parfum::class);
    }
}