<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'old_id',
        'customer_old_id',
        'invoice_type',
        'invoice_date',
        'invoice_received_date',
        'date_due',
        'invoice_received_person',
        'invoice_rec_responsible',
        'invoice_cre_responsible',
        'note',
        'payment_cash',
        'void',
        'printed',
        'paid',
        'tipsdelka',
        'sale_type',
        'pay_method',
        'zero_explain',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_received_date' => 'date',
        'date_due' => 'date',
        'payment_cash' => 'boolean',
        'void' => 'boolean',
        'printed' => 'boolean',
        'paid' => 'boolean',
    ];

    /**
     * Логическа връзка с позиции (invoice_items)
     * invoices.old_id → invoice_items.invoice_old_id
     */
    public function items()
    {
        return $this->hasMany(
            InvoiceItem::class,
            'invoice_old_id', // FK в invoice_items
            'old_id'          // local key в invoices
        );
    }


    /**
     * Логическа връзка с клиент
     * invoices.customer_old_id → customers.old_id
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_old_id', 'old_id');
    }

    /**
     * Doctype (без FK)
     */
    public function doctype()
    {
        return $this->belongsTo(Doctype::class, 'invoice_type', 'type');
    }

    /**
     * Обща сума на фактурата
     */
    public function getTotalAttribute(): float
    {
        return $this->items->sum('row_total');
    }
}
