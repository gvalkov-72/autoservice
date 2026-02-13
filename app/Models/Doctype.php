<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctype extends Model
{
    use HasFactory;

    protected $primaryKey = 'type';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'type',
        'name',
        'short',
        'ddstype',
        'ajurtype',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
