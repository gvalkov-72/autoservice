<?php
// app/Models/Customer.php
// ПЪЛЕН АКТУАЛИЗИРАН ФАЙЛ

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Поля за миграция
        'old_id',
        'customer_number',
        
        // Основни данни
        'name',
        'email',
        'phone',
        'fax',
        
        // Адреси
        'address',
        'address_2',
        'res_address_1',
        'res_address_2',
        
        // Юридически данни
        'contact_person',
        'mol',
        'tax_number',
        'bulstat',
        'doc_type',
        
        // Получател
        'receiver',
        'receiver_details',
        
        // Допълнителни полета
        'eidale',
        'include_in_mailing',
        'partida',
        'bulsial_letter',
        
        // Флагове
        'is_active',
        'is_customer',
        'is_supplier',
        
        // Бележки
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_customer' => 'boolean',
        'is_supplier' => 'boolean',
        'include_in_mailing' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_address',
        'res_full_address',
        'legal_info',
    ];

    /**
     * Get the vehicles for the customer.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the invoices for the customer.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the work orders for the customer.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get the payments for the customer.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include customers (not suppliers).
     */
    public function scopeCustomers($query)
    {
        return $query->where('is_customer', true);
    }

    /**
     * Scope a query to only include suppliers.
     */
    public function scopeSuppliers($query)
    {
        return $query->where('is_supplier', true);
    }

    /**
     * Scope a query to search customers.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('customer_number', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('tax_number', 'like', "%{$search}%")
              ->orWhere('bulstat', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full address attribute.
     */
    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->address) $parts[] = $this->address;
        if ($this->address_2) $parts[] = $this->address_2;
        return implode(', ', $parts);
    }

    /**
     * Get the full reserve address attribute.
     */
    public function getResFullAddressAttribute()
    {
        $parts = [];
        if ($this->res_address_1) $parts[] = $this->res_address_1;
        if ($this->res_address_2) $parts[] = $this->res_address_2;
        return implode(', ', $parts);
    }

    /**
     * Get the legal information attribute.
     */
    public function getLegalInfoAttribute()
    {
        $info = [];
        if ($this->tax_number) $info[] = 'ДДС: ' . $this->tax_number;
        if ($this->bulstat) $info[] = 'Булстат: ' . $this->bulstat;
        if ($this->mol) $info[] = 'МОЛ: ' . $this->mol;
        if ($this->doc_type) $info[] = 'Док. тип: ' . $this->doc_type;
        return implode(' | ', $info);
    }

    /**
     * Get the contact information attribute.
     */
    public function getContactInfoAttribute()
    {
        $info = [];
        if ($this->phone) $info[] = 'Тел: ' . $this->phone;
        if ($this->fax) $info[] = 'Факс: ' . $this->fax;
        if ($this->email) $info[] = 'Email: ' . $this->email;
        if ($this->contact_person) $info[] = 'Контакт: ' . $this->contact_person;
        return implode(' | ', $info);
    }

    /**
     * Check if customer has any activity.
     */
    public function hasActivity()
    {
        return $this->invoices()->count() > 0 
            || $this->workOrders()->count() > 0 
            || $this->vehicles()->count() > 0;
    }

    /**
     * Get the total spent by customer.
     */
    public function getTotalSpentAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    /**
     * Get the total paid by customer.
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get the balance of customer.
     */
    public function getBalanceAttribute()
    {
        return $this->total_spent - $this->total_paid;
    }

    /**
     * Get the last invoice date.
     */
    public function getLastInvoiceDateAttribute()
    {
        $lastInvoice = $this->invoices()->latest('invoice_date')->first();
        return $lastInvoice ? $lastInvoice->invoice_date : null;
    }
}