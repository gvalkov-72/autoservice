<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'received_date' => 'date',
        'date_due' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'payment_cash' => 'decimal:2',
        'is_void' => 'boolean',
        'is_printed' => 'boolean',
        'is_paid' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
        'payment_status' => 'pending',
        'invoice_type' => 'standard',
        'subtotal' => 0,
        'tax_amount' => 0,
        'total_tax_amount' => 0,
        'discount_amount' => 0,
        'grand_total' => 0,
        'payment_cash' => 0,
        'is_void' => false,
        'is_printed' => false,
        'is_paid' => false,
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the customer that owns the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the invoice items for the invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope a query to only include pending invoices.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'overdue')
            ->orWhere(function ($query) {
                $query->where('payment_status', 'pending')
                    ->whereDate('due_date', '<', now());
            });
    }

    /**
     * Calculate the remaining balance of the invoice.
     */
    public function getRemainingBalanceAttribute(): float
    {
        $paidAmount = $this->payments()->sum('amount');
        return max(0, $this->grand_total - $paidAmount);
    }

    /**
     * Check if invoice is fully paid.
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Check if invoice is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_paid && $this->due_date && $this->due_date < now();
    }

    /**
     * Get formatted invoice number with prefix.
     */
    public function getFormattedInvoiceNumberAttribute(): string
    {
        return 'INV-' . str_pad($this->invoice_number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the total items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }
}
