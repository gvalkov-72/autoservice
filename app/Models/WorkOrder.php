<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $fillable = [
        'number',
        'customer_id',
        'vehicle_id',
        'status',
        'received_at',
        'km_on_receive',
        'assigned_to',
        'notes',
        'estimated_completion', // провери дали имаш това поле
        'total_without_vat',
        'vat_amount',
        'total',
        'created_by',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'km_on_receive' => 'integer',
        'estimated_completion' => 'date', // добави това ако имаш полето
        'total_without_vat' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ... останалите методи остават същите
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}