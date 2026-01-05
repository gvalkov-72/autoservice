<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceExportController extends Controller
{
    /**
     * Експорт на фактури в PDF
     * - всички фактури
     * - или само избрани (bulk)
     */
    public function exportPdf(Request $request)
    {
        // Ако има подадени ID-та → bulk export
        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            
            // Преобразуваме в масив от цели числа
            $ids = array_map('intval', $ids);
            $ids = array_filter($ids); // Премахваме празни стойности

            $invoices = Invoice::with(['customer', 'items'])
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->get();

            if ($invoices->isEmpty()) {
                return redirect()
                    ->route('admin.invoices.index')
                    ->with('error', 'Няма валидни избрани фактури.');
            }

            // Използваме bulk-pdf view за всички избрани фактури
            $pdf = Pdf::loadView('admin.invoices.bulk-pdf', compact('invoices'))
                ->setPaper('A4', 'portrait');

            // Показваме PDF в браузъра (stream) вместо да сваляме
            return $pdf->stream('izbrani_fakturi_' . date('Y-m-d_H-i') . '.pdf');
        }

        // Експорт на всички фактури
        $invoices = Invoice::with(['customer', 'items'])
            ->orderByDesc('id')
            ->get();

        if ($invoices->isEmpty()) {
            return redirect()
                ->route('admin.invoices.index')
                ->with('error', 'Няма налични фактури.');
        }

        // Използваме bulk-pdf view за всички фактури
        $pdf = Pdf::loadView('admin.invoices.bulk-pdf', compact('invoices'))
            ->setPaper('A4', 'portrait');

        // Показваме PDF в браузъра (stream) вместо да сваляме
        return $pdf->stream('vsichki_fakturi_' . date('Y-m-d_H-i') . '.pdf');
    }
}