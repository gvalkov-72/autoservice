<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class WorkOrderItemImportSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('old-database/POitems.txt');

        if (!File::exists($file)) {
            $this->command->error("❌ Липсва файл: {$file}");
            return;
        }

        $this->command->info('========================================');
        $this->command->info('🚀 IMPORT WORK ORDER ITEMS');
        $this->command->info('========================================');

        $handle = fopen($file, 'r');

        $imported = 0;
        $batch = [];
        $batchSize = 500;

        while (($line = fgets($handle)) !== false) {

            $line = trim($line);

            if ($line === '' || str_contains($line, 'POID')) {
                continue;
            }

            // 👉 Access TXT = TAB / multiple spaces
            $columns = preg_split('/\s{2,}|\t/', $line);

            if (count($columns) < 7) {
                continue;
            }

            [
                $poId,
                $rowNumber,
                $itemCode,
                $itemName,
                $itemMeasure,
                $qty,
                $priceEach,
                $rowTotal
            ] = array_pad($columns, 8, null);

            $qty        = (float) str_replace(',', '.', $qty ?? 0);
            $priceEach = (float) str_replace(',', '.', $priceEach ?? 0);
            $rowTotal  = (float) str_replace(',', '.', $rowTotal ?? 0);

            $batch[] = [
                'work_order_old_id' => (int) $poId,
                'row_number'        => (int) $rowNumber,
                'item_code'         => $itemCode ?: null,
                'item_name'         => $itemName ?: null,
                'item_measure'      => $itemMeasure ?: null,
                'quantity'          => $qty,
                'price_each'        => $priceEach,
                'row_total'         => $rowTotal,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('work_order_items')->insert($batch);
                $imported += count($batch);
                $batch = [];

                $this->command->info("➕ {$imported} реда");
            }
        }

        if ($batch) {
            DB::table('work_order_items')->insert($batch);
            $imported += count($batch);
        }

        fclose($handle);

        $this->command->info('========================================');
        $this->command->info("✅ ГОТОВО | Импортирани редове: {$imported}");
        $this->command->info('========================================');
    }
}
