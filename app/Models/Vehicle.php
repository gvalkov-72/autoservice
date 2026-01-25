<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'customer_id',
        'vehicle',
        'plate_number',
        'chassis_number',
        'mileage',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mileage' => 'integer',
    ];

    /**
     * Връзка с клиента
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Връзка с работни поръчки
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Гетър за пълно описание на автомобила
     */
    public function getFullDescriptionAttribute(): string
    {
        $parts = [];
        
        if ($this->vehicle) {
            $parts[] = $this->vehicle;
        }
        
        if ($this->plate_number) {
            $parts[] = '(' . $this->plate_number . ')';
        }
        
        if ($this->chassis_number) {
            $parts[] = 'VIN: ' . $this->chassis_number;
        }
        
        return implode(' ', $parts);
    }
}