<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

class WorkOrderInvoiceService
{
    public static function createInvoiceFromWorkOrder(WorkOrder $workOrder): Invoice
    {
        if ($workOrder->invoice_id) {
            throw new \Exception('Тази работна карта вече е фактурирана.');
        }

        return DB::transaction(function () use ($workOrder) {

            // 1. Създаваме фактура
            $invoice = Invoice::create([
                'customer_id' => $workOrder->customer_id,
                'vehicle_id'  => $workOrder->vehicle_id,
                'invoice_date' => now(),
                'status' => 'draft',
            ]);

            // 2. Копираме редовете от WorkOrderItem → InvoiceItem
            foreach ($workOrder->items as $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total,
                    'vat_rate' => 20,
                    'vat_amount' => ($item->total * 0.20),
                ]);
            }

            // 3. Обновяваме тоталите
            $subtotal = $invoice->items()->sum('total_price');
            $vat = $invoice->items()->sum('vat_amount');

            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $vat,
                'total_tax_amount' => $vat,
                'grand_total' => $subtotal + $vat,
            ]);

            // 4. Заключваме работната карта
            $workOrder->update([
                'invoice_id' => $invoice->id,
                'status' => 'invoiced',
            ]);

            return $invoice;
        });
    }
}
