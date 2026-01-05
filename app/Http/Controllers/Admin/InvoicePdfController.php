<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\CompanySetting;
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
            'workOrder',
        ]);

        // Вземаме настройките на компанията
        $companySettings = CompanySetting::first();

        // Генерираме PDF от admin view
        $pdf = Pdf::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ])->setPaper('A4', 'portrait');

        // Добавяме опции за футер
        $pdf->setOption('margin-bottom', '15mm');
        
        // Показване в браузъра с отваряне в нов прозорец
        return $pdf->stream(
            'invoice_' . $invoice->invoice_number . '.pdf',
            ['Attachment' => false]
        );
    }

    /**
     * Експорт на списък с фактури (PDF)
     */
    public function exportList($invoices)
    {
        $pdf = Pdf::loadView('admin.invoices.bulk-pdf', [
            'invoices' => $invoices,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('invoices.pdf');
    }
}