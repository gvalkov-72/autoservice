<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'old_id',
        'customer_number',
        'name',
        'email',
        'phone',
        'fax',
        'address',
        'address_2',
        'res_address_1',
        'res_address_2',
        'mol',
        'contact_person',
        'tax_number',
        'bulstat',
        'bulstat_letter',
        'doc_type',
        'receiver',
        'receiver_details',
        'eidate',
        'partida',
        'notes',
        'include_in_mailing',
        'is_active',
        'is_customer',
        'is_supplier',
    ];

    protected $casts = [
        'include_in_mailing' => 'boolean',
        'is_active' => 'boolean',
        'is_customer' => 'boolean',
        'is_supplier' => 'boolean',
        'eidate' => 'date',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relations (ще се използват по-късно)
     |--------------------------------------------------------------------------
     */

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
