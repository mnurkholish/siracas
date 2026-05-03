<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'kecamatan_id',
        'detail_alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function fullAddress(): string
    {
        return collect([
            $this->detail_alamat,
            $this->kecamatan?->nama,
            $this->kecamatan?->kota?->nama,
            $this->kecamatan?->kota?->provinsi?->nama,
        ])->filter()->implode(', ');
    }
}
