<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    /**
     * Показва PDF на фактура
     */
    public function show(Invoice $invoice)
    {
        // Зареждаме всички нужни връзки предварително
        $invoice->load([
            'customer',
            'items',
            'payments',
        ]);

        // Генериране на PDF от Blade изгледа
        $pdf = Pdf::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        // Име на файла при изтегляне
        $fileName = 'invoice_' . $invoice->invoice_number . '.pdf';

        // Показване директно в браузъра
        return $pdf->stream($fileName);
    }
}
