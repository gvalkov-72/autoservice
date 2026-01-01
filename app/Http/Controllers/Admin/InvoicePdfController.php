<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    /**
     * Показване на PDF фактура
     */
    public function show(Invoice $invoice)
    {
        // Зареждаме нужните релации
        $invoice->load([
            'customer',
            'items',
            'payments',
        ]);

        // Генерираме PDF от admin view
        $pdf = Pdf::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
        ])->setPaper('A4', 'portrait');

        // Показване в браузъра
        return $pdf->stream(
            'invoice_' . $invoice->invoice_number . '.pdf'
        );
    }
}
