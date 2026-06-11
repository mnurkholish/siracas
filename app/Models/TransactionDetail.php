<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionDetail extends Model
{
    protected $table = 'transaction_details';

    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'harga_saat_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'transaction_id' => 'integer',
            'product_id' => 'integer',
            'quantity' => 'integer',
            'harga_saat_transaksi' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function subtotal(): float
    {
        return $this->quantity * (float) $this->harga_saat_transaksi;
    }
}
