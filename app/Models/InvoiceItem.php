<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Атрибути, които могат да бъдат попълвани масово
     */
    protected $fillable = [
        'invoice_id',
        'line_number',
        'product_code',
        'description',
        'unit_of_measure',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * Кастване на типове
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Връзка към фактурата
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Връзка към продукт (ако имаме product_code)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
}