<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'transaction_detail_id',
        'isi',
        'rating',
        'foto',
        'admin_reply',
        'admin_replied_at',
        'admin_replied_by',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'admin_replied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function transactionDetail(): BelongsTo
    {
        return $this->belongsTo(TransactionDetail::class);
    }

    public function adminReplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_replied_by');
    }
}
