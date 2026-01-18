<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\InvoicePaymentCalculator;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        InvoicePaymentCalculator::recalculate($payment->invoice);
    }

    public function deleted(Payment $payment): void
    {
        InvoicePaymentCalculator::recalculate($payment->invoice);
    }

    public function updated(Payment $payment): void
    {
        InvoicePaymentCalculator::recalculate($payment->invoice);
    }
}
