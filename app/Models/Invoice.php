<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'old_id',
        'customer_id',
        'work_order_id',
        'customer_old_id',
        'invoice_type',
        'invoice_date',
        'invoice_received_date',
        'date_due',
        'invoice_received_person',
        'invoice_created_by',
        'invoice_rec_responsible',
        'invoice_cre_responsible',
        'note',
        'zeroexplain',
        'payment_cash',
        'is_void',
        'printed',
        'paid',
        'tipsdelka',
        'sale_type',
        'pay_method',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_received_date' => 'date',
        'date_due' => 'date',
        'payment_cash' => 'boolean',
        'is_void' => 'boolean',
        'printed' => 'boolean',
        'paid' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Връзка с клиент чрез новото customer_id
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Стара връзка чрез old_id (ако все още трябва)
     */
    public function customerByOldId()
    {
        return $this->belongsTo(Customer::class, 'customer_old_id', 'old_id');
    }

    /**
     * Връзка с тип документ (doctype)
     */
    public function doctype()
    {
        return $this->belongsTo(Doctype::class, 'invoice_type', 'type');
    }

    /**
     * Артикули по фактурата
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_old_id', 'old_id');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    /**
     * Помощна: обща сума на фактурата
     */
    public function getTotalAttribute()
    {
        return $this->items->sum('row_total');
    }
}
