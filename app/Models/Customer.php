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

    // ========== RELATIONS ==========
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

    // ========== SCOPES ==========
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustomers($query)
    {
        return $query->whereIn('type', ['customer', 'both']);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }

    // ========== ACCESSORS ==========
    public function getTypeLabelAttribute()
    {
        return [
            'customer' => 'Клиент',
            'supplier' => 'Доставчик',
            'both' => 'Клиент и Доставчик',
        ][$this->type] ?? $this->type;
    }

    public function getFullBulstatAttribute()
    {
        if (!$this->bulstat) return null;
        $prefix = $this->bulstat_letter ? "BG{$this->bulstat_letter}" : 'BG';
        return $prefix . $this->bulstat;
    }

    public function getFormattedAddressAttribute()
    {
        if ($this->address) return $this->address;
        return trim($this->address_line1 . ', ' . $this->address_line2, ', ');
    }

    // ========== HELPERS ==========
    public function isActive()
    {
        return $this->is_active === true;
    }

    public function shouldIncludeInReports()
    {
        return $this->include_in_reports === true;
    }
}