<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_settings';

    protected $fillable = [
        'name',
        'city',
        'address',
        'vat_number',
        'contact_person',
        'iban',
        'bank_name',
        'bic',
        'phone',
        'email',
        'website',
        'invoice_footer',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Връща пълния адрес като низ.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];
        if ($this->city) $parts[] = $this->city;
        if ($this->address) $parts[] = $this->address;
        return implode(', ', $parts) ?: '—';
    }

    /**
     * Връща ДДС номера с префикс BG (ако не е въведен).
     */
    public function getVatNumberFormattedAttribute(): string
    {
        if (!$this->vat_number) return '—';
        return str_starts_with($this->vat_number, 'BG') ? $this->vat_number : 'BG' . $this->vat_number;
    }

    /**
     * Връща пътя до логото или placeholder.
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path 
            ? asset('storage/' . $this->logo_path) 
            : asset('images/no-logo.png');
    }
}