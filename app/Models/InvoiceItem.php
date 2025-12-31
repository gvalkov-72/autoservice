<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'line_number',
        'product_code',
        'description',
        'unit_of_measure',
        'quantity',
        'unit_price',
        'vat_rate',
        'vat_amount',
        'total_price',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'vat_rate'     => 'decimal:2',
        'vat_amount'   => 'decimal:2',
        'total_price'  => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Изчислява ДДС и крайна цена за ред
     */
    public function calculateTotals(): void
    {
        $net = $this->quantity * $this->unit_price;

        $this->vat_amount = round(
            $net * ($this->vat_rate / 100),
            2
        );

        $this->total_price = round(
            $net + $this->vat_amount,
            2
        );
    }
}
