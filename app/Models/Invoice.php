<?php

namespace App\Models;

use App\Services\InvoiceNumberGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * Полета за масово попълване
     */
    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'received_date',
        'date_due',
        'status',
        'payment_status',
        'payment_method',
        'invoice_type',
        'sale_type',
        'subtotal',
        'tax_amount',
        'total_tax_amount',
        'discount_amount',
        'grand_total',
        'payment_cash',
        'payment_iod',
        'is_void',
        'is_printed',
        'is_paid',
        'received_person',
        'invoice_rec_responsible',
        'invoice_cre_responsible',
        'notes',
        'terms',
        'zero_explain',
        'additional_info',
        'tips_deka',
    ];

    /**
     * Кастове
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'received_date' => 'date',
        'date_due' => 'date',
        'is_void' => 'boolean',
        'is_printed' => 'boolean',
        'is_paid' => 'boolean',
    ];

    /**
     * 🔑 АВТОМАТИЧНО ГЕНЕРИРАНЕ НА НОМЕР НА ФАКТУРА
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = InvoiceNumberGenerator::next();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function workOrder()
    {
        return $this->hasOne(WorkOrder::class);
    }


    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
