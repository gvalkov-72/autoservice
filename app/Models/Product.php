<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'old_id',
        'name',
        'uom',
        'quantity',
        'price',
        'account',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relations (ще се използват по-късно)
     |--------------------------------------------------------------------------
     */

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function workOrderItems()
    {
        return $this->hasMany(WorkOrderItem::class);
    }
}
