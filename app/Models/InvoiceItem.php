<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_old_id',
        'number',
        'item_code',
        'item_name',
        'item_measure',
        'item_qty',
        'item_price_each',
        'item_total',
    ];

    protected $casts = [
        'item_qty' => 'float',
        'item_price_each' => 'float',
        'item_total' => 'float',
    ];

    /**
     * Обратна логическа връзка към фактура
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_old_id', 'old_id');
    }
}
