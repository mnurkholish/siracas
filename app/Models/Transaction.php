<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'transactions';

    public const STATUSES = [
        'menunggu_pembayaran',
        'dibayar',
        'diproses',
        'dikirim',
        'selesai',
        'dibatalkan',
        'kedaluwarsa',
    ];

    public const ACTIVE_STATUSES = [
        'menunggu_pembayaran',
        'dibayar',
        'diproses',
        'dikirim',
    ];

    public const HISTORY_STATUSES = [
        'selesai',
        'dibatalkan',
        'kedaluwarsa',
    ];

    protected $fillable = [
        'user_id',
        'address_id',
        'tanggal',
        'catatan',
        'status',
        'ongkir',
        'order_id',
        'snap_token',
        'payment_type',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'paid_at' => 'datetime',
            'ongkir' => 'decimal:2',
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
        return (float) $this->transactionDetails->sum(function (TransactionDetail $detail) {
            return $detail->quantity * (float) $detail->harga_saat_transaksi;
        });
    }

    public function totalAkhir(): float
    {
        return $this->totalHarga() + (float) $this->ongkir;
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
