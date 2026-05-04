<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'transactions';

    public const STATUSES = [
        'pending',
        'paid',
        'processing',
        'completed',
        'cancelled',
        'expired',
    ];

    protected $fillable = [
        'user_id',
        'address_id',
        'city',
        'province',
        'ongkir',
        'total_barang',
        'total_bayar',
        'tanggal',
        'catatan',
        'status',
        'order_id',
        'snap_token',
        'payment_type',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'ongkir' => 'decimal:2',
            'total_barang' => 'decimal:2',
            'total_bayar' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function totalHarga(): float
    {
        if ($this->total_barang !== null) {
            return (float) $this->total_barang;
        }

        return (float) $this->transactionDetails->sum(fn (TransactionDetail $detail) => $detail->subtotal());
    }

    public function totalBayar(): ?float
    {
        return $this->total_bayar === null ? null : (float) $this->total_bayar;
    }

    public function totalQuantity(): int
    {
        return (int) $this->transactionDetails->sum('quantity');
    }

    public function ringkasanProduk(): string
    {
        $products = $this->transactionDetails
            ->map(fn (TransactionDetail $detail) => $detail->product?->nama_produk)
            ->filter()
            ->values();

        if ($products->isEmpty()) {
            return '-';
        }

        $first = $products->first();
        $remaining = $products->count() - 1;

        return $remaining > 0 ? "{$first} +{$remaining} produk" : $first;
    }
}
