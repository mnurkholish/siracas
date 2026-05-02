<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function totalItems(): int
    {
        return (int) $this->cartItems->sum('quantity');
    }

    public function totalHarga(): float
    {
        return (float) $this->cartItems->sum(function (CartItem $item) {
            return $item->quantity * (float) $item->harga_saat_dimasukkan;
        });
    }
}
