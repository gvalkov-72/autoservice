<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_old_id',
        'row_number',
        'item_code',
        'item_name',
        'item_measure',
        'quantity',
        'price_each',
        'row_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_each' => 'decimal:2',
        'row_total' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_old_id', 'old_id');
    }
}