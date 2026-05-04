<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ongkir extends Model
{
    protected $table = 'ongkir';

    protected $fillable = [
        'tipe',
        'nama',
        'harga',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function setNamaAttribute(string $value): void
    {
        $this->attributes['nama'] = self::normalizeNama($value);
    }

    public static function normalizeNama(?string $value): string
    {
        return Str::of($value ?? '')
            ->lower()
            ->squish()
            ->toString();
    }
}
