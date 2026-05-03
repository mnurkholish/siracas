<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $table = 'kecamatans';

    protected $fillable = [
        'kota_id',
        'code',
        'nama',
    ];

    public function kota(): BelongsTo
    {
        return $this->belongsTo(Kota::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
