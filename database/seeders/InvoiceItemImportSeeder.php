<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class InvoiceItemImportSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('old-database/Item.txt');

        if (!File::exists($file)) {
            $this->command->error('❌ Файлът Item.txt не е намерен');
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $total = 0;
        $insert = [];

        $this->command->info(str_repeat('=', 40));
        $this->command->info('🚀 IMPORT: INVOICE ITEMS (Access → Laravel)');
        $this->command->info(str_repeat('=', 40));

        foreach ($lines as $line) {

            // пропускаме header / separator линии
            if (
                str_contains($line, 'Invoice-ID') ||
                str_starts_with(trim($line), '----')
            ) {
                continue;
            }

            // махаме началното и крайното |
            $line = trim($line, "|\t ");

            $cols = array_map('trim', explode('|', $line));

            if (count($cols) < 8) {
                continue;
            }

            [
                $invoiceId,
                $rowNumber,
                $itemCode,
                $itemName,
                $itemMeasure,
                $qty,
                $priceEach,
                $rowTotal
            ] = $cols;

            // числови стойности (замяна , → .)
            $qty = (float) str_replace(',', '.', $qty);
            $priceEach = (float) str_replace(',', '.', $priceEach);
            $rowTotal = (float) str_replace(',', '.', $rowTotal);

            // fallback ако total липсва
            if ($rowTotal == 0 && $qty > 0 && $priceEach > 0) {
                $rowTotal = $qty * $priceEach;
            }

            $insert[] = [
                'invoice_old_id' => (int) $invoiceId,
                'row_number'     => (int) $rowNumber,
                'item_code'      => $itemCode ?: null,
                'item_name'      => $itemName,
                'item_measure'   => $itemMeasure ?: null,
                'quantity'       => $qty,
                'price_each'     => $priceEach,
                'row_total'      => $rowTotal,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            $total++;

            // batch insert (по 500)
            if (count($insert) === 500) {
                DB::table('invoice_items')->insert($insert);
                $insert = [];
                $this->command->info("➕ Импортирани редове: {$total}");
            }
        }

        if (!empty($insert)) {
            DB::table('invoice_items')->insert($insert);
        }

        $this->command->info(str_repeat('=', 40));
        $this->command->info("✅ IMPORT FINISHED | Общо редове: {$total}");
        $this->command->info(str_repeat('=', 40));
    }
}
