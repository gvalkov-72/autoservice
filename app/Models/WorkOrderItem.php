<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $table = 'work_order_items';

    protected $fillable = [
        'work_order_id',
        'work_order_old_id',
        'row_number',
        'item_code',
        'item_name',
        'item_measure',
        'quantity',
        'price_each',
        'row_total',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'price_each' => 'decimal:2',
        'row_total'  => 'decimal:2',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
