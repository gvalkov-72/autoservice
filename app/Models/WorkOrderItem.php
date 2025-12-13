<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderItem extends Model
{
    protected $fillable = [
        'work_order_id',
        'product_id',
        'service_id',
        'description',
        'quantity',
        'unit_price',
        'vat_percent',
        'line_total_without_vat',
        'line_vat_amount',
        'line_total',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ДОБАВЕН МЕТОД:
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}