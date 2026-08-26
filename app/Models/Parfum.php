<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parfum extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'harga_per_ml',
        'stok',
        'kategori',
        'foto',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'harga_per_ml' => 'decimal:2',
    ];

    public function pesananItems()
    {
        return $this->hasMany(PesananItem::class);
    }
}