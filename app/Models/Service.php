<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'vat_percent',
        'duration_minutes',
        'is_active',
        'category',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    // ДОБАВЕН МЕТОД:
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}