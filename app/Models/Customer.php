<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'old_system_id',
        'type',
        'name',
        'vat_number',
        'bulstat',
        'contact_person',
        'phone',
        'fax',
        'email',
        'address',
        'address_line1',
        'address_line2',
        'city',
        'notes',
        'court_registration',
        'bulstat_letter',
        'is_active',
        'include_in_reports',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'include_in_reports' => 'boolean',
    ];

    // Останалите методи остават същите...
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}