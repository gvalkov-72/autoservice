<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'bank_id',
        'payment_method_id',
        'amount',
        'paid_at',
        'reference',
        'created_by',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount'  => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    protected static function booted()
    {
        static::created(function (Payment $payment) {
            $payment->invoice?->refreshPaymentStatus();
        });

        static::deleted(function (Payment $payment) {
            $payment->invoice?->refreshPaymentStatus();
        });
    }
}
