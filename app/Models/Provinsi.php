<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    protected $table = 'provinsis';

    protected $fillable = [
        'code',
        'nama',
    ];

    public function kotas(): HasMany
    {
        return $this->hasMany(Kota::class);
    }
}
