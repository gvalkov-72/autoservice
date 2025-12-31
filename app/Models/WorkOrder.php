<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'customer_id',
        'vehicle_id',
        'status',
        'received_at',
        'km_on_receive',
        'assigned_to',
        'total_without_vat',
        'vat_amount',
        'total',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'total_without_vat' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Клиент
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Автомобил
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }


    /**
     * Назначен механик (user)
     */
    public function mechanic()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Създадено от (user)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Редове на работната поръчка (дейности / части)
     */
    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }
}
