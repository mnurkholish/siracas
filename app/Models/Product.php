<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'harga',
        'stok',
        'satuan',
        'deskripsi',
        'foto',
    ];

    public const SATUAN = [
        'kg' => 'Kg',
        'gram' => 'Gram',
        'pcs' => 'Pcs',
        'paket' => 'Paket',
        'karung' => 'Karung',
        'ton' => 'Ton',
        'kuwintal' => 'Kuwintal',
        'botol' => 'Botol',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'stok' => 'integer',
        ];
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
