<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'nama',
        'developer',
        'tahun',
        'rilis',
        'genre',
        'harga',
        'gambar',
    ];
}
