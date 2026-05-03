<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kota extends Model
{
    protected $table = 'kotas';

    protected $fillable = [
        'provinsi_id',
        'code',
        'nama',
    ];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatans(): HasMany
    {
        return $this->hasMany(Kecamatan::class);
    }
}
