<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    public $incrementing = false; // защото ID идва от Access
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'account',
        'bank_code',
        'name',
        'currency',
        'method',
        'type',
        'short_name',
        'is_default',
        'active',
    ];
}
