<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;

class WorkOrderImportSeeder extends Seeder
{
    /**
     * Създава work_orders от съществуващи invoices
     * и копира invoice_items -> work_order_items
     */
    public function run(): void
    {
        DB::transaction(function () {

            $this->command->info('Започва импорт на сервизни поръчки от фактури...');

            $invoices = Invoice::with(['items'])
                ->whereNull('work_order_id')
                ->get();

            foreach ($invoices as $invoice) {

                // 1. Създаваме сервизна поръчка
                $workOrder = WorkOrder::create([
                    'customer_id' => $invoice->customer_id,
                    'vehicle_id'  => $invoice->vehicle_id,
                    'user_id'     => $invoice->user_id ?? 1, // fallback към админ
                    'status'      => 'completed',
                    'notes'       => 'Автоматично създадена от фактура #' . $invoice->id,
                ]);

                // 2. Копираме редовете от фактурата като ремонти
                foreach ($invoice->items as $item) {

                    WorkOrderItem::create([
                        'work_order_id' => $workOrder->id,
                        'line_number'   => $item->line_number,
                        'description'   => $item->description,
                        'quantity'      => $item->quantity,
                        'unit_price'    => $item->unit_price,
                        'total_price'   => $item->total_price,
                    ]);
                }

                // 3. Връзваме фактурата към поръчката
                $invoice->update([
                    'work_order_id' => $workOrder->id,
                ]);

                $this->command->info(
                    "✔ Фактура #{$invoice->id} → WorkOrder #{$workOrder->id}"
                );
            }

            $this->command->info('✔ Импортът на сервизни поръчки приключи успешно.');
        });
    }
}
