<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkOrderTotalSeeder extends Seeder
{
    public function run(): void
    {
        echo PHP_EOL;
        echo "========================================" . PHP_EOL;
        echo "🧮 CALCULATE WORK ORDER TOTALS" . PHP_EOL;
        echo "========================================" . PHP_EOL;

        $batchSize = 500;
        $offset = 0;
        $updated = 0;

        while (true) {

            $workOrders = DB::table('work_orders')
                ->orderBy('id')
                ->offset($offset)
                ->limit($batchSize)
                ->get();

            if ($workOrders->isEmpty()) {
                break;
            }

            foreach ($workOrders as $wo) {

                $itemsTotal = DB::table('work_order_items')
                    ->where('work_order_id', $wo->id)
                    ->sum('row_total');

                $serviceAmount = $wo->service_amount ?? 0;

                $total = round($itemsTotal + $serviceAmount, 2);

                DB::table('work_orders')
                    ->where('id', $wo->id)
                    ->update([
                        'total' => $total,
                        'updated_at' => now(),
                    ]);

                $updated++;
            }

            $offset += $batchSize;
            echo "➕ Обработени поръчки: {$updated}" . PHP_EOL;
        }

        echo "========================================" . PHP_EOL;
        echo "✅ TOTALS CALCULATED | Общо: {$updated}" . PHP_EOL;
        echo "========================================" . PHP_EOL;
    }
}
