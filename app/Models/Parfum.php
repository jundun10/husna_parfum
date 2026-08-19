<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parfum extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'stok',
        'kategori',
        'foto',
    ];
}