<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeranjangItem extends Model
{
    protected $fillable = [
        'user_id',
        'parfum_id',
        'jumlah',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parfum(): BelongsTo
    {
        return $this->belongsTo(Parfum::class);
    }
}