<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCampaign extends Model
{
    public const TYPES = [
        'promo' => 'Promo',
        'product' => 'Produk Baru',
        'announcement' => 'Pengumuman',
    ];

    protected $fillable = [
        'type',
        'title',
        'message',
        'url',
        'image',
        'is_active',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
