<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InvoiceItemImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 СТАРТИРАНЕ НА ИМПОРТ НА АРТИКУЛИ ОТ TXT ФАЙЛ');
        $this->command->info('========================================');

        // КОРИГИРАН ПЪТ - същият като в InvoiceImportSeeder
        $filePath = base_path('old-database/invoice_items.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ ФАЙЛЪТ НЕ Е НАМЕРЕН: invoice_items.txt');
            $this->command->info('💡 Очакван път: ' . $filePath);
            return;
        }

        $this->command->info('📁 Прочитане на файл: ' . $filePath);

        // Изтриване на съществуващи данни
        $this->command->info('🗑️  Изтриване на стари записи...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('invoice_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Четем целия файл
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);

        $totalLines = count($lines);
        $this->command->info("📊 Общо редове във файла: {$totalLines}");

        $importedCount = 0;
        $skippedCount = 0;

        $this->command->info('🔍 Търсене на редове с данни...');

        // Търсим мястото, където започват данните (както в InvoiceImportSeeder)
        $startIndex = -1;
        for ($i = 0; $i < min(50, $totalLines); $i++) {
            $line = trim($lines[$i]);

            // Търсим линия, която започва с "|         1 |" (първи запис)
            if (preg_match('/^\|\s+\d+\s+\|/', $line)) {
                $startIndex = $i;
                $this->command->info("✅ Данните започват на ред: " . ($i + 1));
                break;
            }
        }

        if ($startIndex === -1) {
            $this->command->error('❌ Не са намерени данни във файла!');
            return;
        }

        $progressBar = $this->command->getOutput()->createProgressBar($totalLines - $startIndex);
        $progressBar->start();

        // Обработваме редовете от startIndex нататък
        for ($i = $startIndex; $i < $totalLines; $i++) {
            $line = trim($lines[$i]);

            if (empty($line)) {
                $progressBar->advance();
                continue;
            }

            // Пропускаме разделителни линии (съставени от -, =, _)
            if (preg_match('/^[-=|_]+$/', $line)) {
                $progressBar->advance();
                continue;
            }

            $progressBar->advance();

            try {
                // Извличане на данните за артикул
                $itemData = $this->parseInvoiceItemLine($line);

                if ($itemData === null) {
                    $skippedCount++;
                    continue;
                }

                // Проверка дали фактурата съществува
                if (!Invoice::where('id', $itemData['invoice_id'])->exists()) {
                    $skippedCount++;
                    continue;
                }

                // Създаване на записа
                InvoiceItem::create($itemData);
                $importedCount++;

            } catch (\Exception $e) {
                $skippedCount++;
            }
        }

        $progressBar->finish();
        $this->command->newLine(2);

        // Резюме
        $this->command->info('✅ ИМПОРТЪТ Е ЗАВЪРШЕН');
        $this->command->info('========================================');
        $this->command->info("📦 Импортирани артикули: {$importedCount}");
        $this->command->info("⏭️  Пропуснати артикули: {$skippedCount}");
        $this->command->info('========================================');
    }

    /**
     * Парсва ред за артикул от фактура
     */
    private function parseInvoiceItemLine(string $line): ?array
    {
        // Премахваме началния и крайния |
        $line = trim($line, '|');

        // Разделяме по |
        $columns = explode('|', $line);

        // Тримваме всяка колона
        $columns = array_map('trim', $columns);

        // Брой на колоните според твоя пример трябва да са 8
        if (count($columns) < 8) {
            return null;
        }

        // Мапиране на колоните според твоя пример:
        // | Invoice-ID | Number | Item-Code | Item-Name | Item | Item | Item-Price-Ea | Item-total |
        //     0            1          2           3         4      5         6              7
        // 4-та колона: единица мярка (ЛИТ)
        // 5-та колона: количество

        $data = [
            'invoice_id' => $this->cleanNumber($columns[0]),
            'line_number' => $this->cleanNumber($columns[1]),
            'product_code' => empty($columns[2]) ? null : $columns[2],
            'description' => $columns[3],
            'unit_of_measure' => $columns[4],  // единица мярка
            'quantity' => $this->parseDecimal($columns[5]),  // количество
            'unit_price' => $this->parseDecimal($columns[6]),
            'total_price' => $this->parseDecimal($columns[7]),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Проверка за валидност
        if (empty($data['invoice_id']) || empty($data['description'])) {
            return null;
        }

        return $data;
    }

    /**
     * Почиства число от празни пространства
     */
    private function cleanNumber(string $value): ?int
    {
        $value = trim($value);
        return $value === '' ? null : (int)$value;
    }

    /**
     * Парсва десетично число (заменя запетая с точка)
     */
    private function parseDecimal(string $value): float
    {
        $value = trim($value);
        $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float)$value : 0.0;
    }
}