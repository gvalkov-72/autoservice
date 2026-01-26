<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_id',
        'client_name',
        'order_date',
        'created_by',
        'note',
        'vehicle',
        'chassis_number',
        'plate_number',
        'phone',
        'mechanic_code',
        'mileage',
        'service_amount',
    ];

    protected $casts = [
        'order_date'     => 'date',
        'service_amount' => 'decimal:2',
    ];

    /**
     * Редове (POitems)
     */
    public function items()
    {
        return $this->hasMany(WorkOrderItem::class, 'work_order_old_id', 'old_id');
    }

    /**
     * Обща сума (труд + редове)
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->service_amount
            + (float) $this->items()->sum('row_total');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
