<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctype extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'short',
        'ddstype',
        'ajurtype',
        'active',
    ];
}
