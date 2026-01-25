<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use Carbon\Carbon;

class WorkOrderImportSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('old-database/PO.txt');

        if (!file_exists($file)) {
            $this->command->error("❌ Файлът не съществува: PO.txt");
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);

        $count = 0;

        foreach ($lines as $line) {

            // прескачаме separator линии
            if (str_starts_with(trim($line), '---')) {
                continue;
            }

            // редовете с данни започват с |
            if (!str_starts_with(trim($line), '|')) {
                continue;
            }

            // махаме първия и последния |
            $columns = array_map('trim', explode('|', trim($line, '|')));

            // header ред
            if ($columns[0] === 'Поръчка') {
                continue;
            }

            /*
             * Колони:
             * 0 => Поръчка
             * 1 => Клиент
             * 2 => PODate
             * 3 => Author
             * 4 => Забележ
             * 5 => Шаси
             * 6 => Телефон
             * 7 => Автомобил
             * 8 => ДК No
             * 9 => Код на монтьора
             * 10 => Изминати км
             * 11 => serviceamt
             */

            $orderDate = null;
            if (!empty($columns[2])) {
                $cleanDate = str_replace('?.', '', $columns[2]);
                try {
                    $orderDate = Carbon::createFromFormat('d.m.Y', trim($cleanDate));
                } catch (\Exception $e) {
                    $orderDate = null;
                }
            }

            WorkOrder::create([
                'old_id'         => (int)$columns[0],
                'client_name'    => $columns[1] ?: null,
                'order_date'     => $orderDate,
                'created_by'     => $columns[3] ?: null,
                'note'           => $columns[4] ?: null,
                'chassis_number' => $columns[5] ?: null,
                'phone'          => $columns[6] ?: null,
                'vehicle'        => $columns[7] ?: null,
                'plate_number'   => $columns[8] ?: null,
                'mechanic_code'  => is_numeric($columns[9]) ? (int)$columns[9] : null,
                'mileage'        => is_numeric($columns[10]) ? (int)$columns[10] : null,
                'service_amount' => is_numeric($columns[11]) ? (float)$columns[11] : 0,
            ]);

            $count++;
        }

        $this->command->info("✅ IMPORT FINISHED | Общо: {$count}");
    }
}
