<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $fillable = [
        'user_id',
        'alamat_id',
        'payment_method_id',
        'total_harga',
        'status',
        'metode_pembayaran',
        'status_pembayaran',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
{
    return $this->belongsTo(PaymentMethod::class);
}

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }
}