<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoicePaymentService
{
    public static function addPayment(Invoice $invoice, array $data): void
    {
        DB::transaction(function () use ($invoice, $data) {

            $invoice->payments()->create([
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date' => $data['payment_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);

            self::recalculateInvoiceStatus($invoice);
        });
    }

    protected static function recalculateInvoiceStatus(Invoice $invoice): void
    {
        $paid = $invoice->payments()->sum('amount');
        $total = $invoice->grand_total;

        if ($paid <= 0) {
            $invoice->update(['payment_status' => 'pending']);
            return;
        }

        if ($paid < $total) {
            $invoice->update(['payment_status' => 'partial']);
            return;
        }

        $invoice->update([
            'payment_status' => 'paid',
            'is_paid' => true,
            'status' => 'paid',
        ]);
    }
}
