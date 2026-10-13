<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'game_platform')
            ->withTimestamps();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
