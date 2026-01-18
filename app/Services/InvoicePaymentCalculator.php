<?php

namespace App\Services;

use App\Models\Invoice;

class InvoicePaymentCalculator
{
    public static function recalculate(Invoice $invoice): void
    {
        $totalPaid = $invoice->payments()->sum('amount');
        $grandTotal = $invoice->grand_total;

        // Определяне на payment_status
        if ($totalPaid <= 0) {
            $paymentStatus = 'pending';
            $isPaid = false;
        } elseif ($totalPaid < $grandTotal) {
            $paymentStatus = 'partial';
            $isPaid = false;
        } else {
            $paymentStatus = 'paid';
            $isPaid = true;
        }

        $invoice->updateQuietly([
            'payment_status' => $paymentStatus,
            'is_paid' => $isPaid,
        ]);
    }
}
