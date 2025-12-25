<?php
// database/seeders/ProductImportSeeder.php
// АКТУАЛИЗИРАН ЗА TXT ФАЙЛОВЕ ОТ ACCESS И СЪВМЕСТИМ С НОВИЯ МОДЕЛ

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 СТАРТИРАНЕ НА ИМПОРТ НА ПРОДУКТИ ОТ TXT ФАЙЛ');
        $this->command->info('========================================');

        $filePath = base_path('old-database/Products.txt');

        // Проверка за файл
        if (!file_exists($filePath)) {
            $this->command->error('❌ ФАЙЛЪТ НЕ Е НАМЕРЕН: Products.txt');
            $this->command->info('📂 Моля поставете Products.txt в папка: ' . dirname($filePath));
            $this->command->info('💡 Файлът трябва да е в табличен формат с вертикални разделители "|"');
            $this->command->info('📋 Очаквани колони: PLU, Name, UOM, Qty, Price, acc');
            return;
        }

        // Прочитане на целия файл
        $content = file_get_contents($filePath);
        if (empty($content)) {
            $this->command->error('❌ ФАЙЛЪТ Е ПРАЗЕН ИЛИ НЕ МОЖЕ ДА БЪДЕ ПРОЧЕТЕН');
            return;
        }

        // Разделяне на редове
        $lines = explode("\n", $content);
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });

        if (count($lines) < 3) {
            $this->command->error('❌ ФАЙЛЪТ НЕ СЪДЪРЖА ДОСТАТЪЧНО ДАННИ');
            return;
        }

        // Намиране на заглавния ред
        $headerLineIndex = null;
        $headerLine = '';

        foreach ($lines as $index => $line) {
            if (strpos($line, '|  PLU   |') !== false || preg_match('/\|\s*PLU\s*\|/i', $line)) {
                $headerLineIndex = $index;
                $headerLine = trim($line);
                break;
            }
        }

        if ($headerLineIndex === null) {
            // Ако не намерим точно "PLU", търсим първия ред, който изглежда като заглавка
            foreach ($lines as $index => $line) {
                if (strpos($line, '|') !== false && substr_count($line, '|') > 3) {
                    if (!preg_match('/^[\|\-\s]+$/', $line)) {
                        $headerLineIndex = $index;
                        $headerLine = trim($line);
                        $this->command->warn('⚠️ Намерен е възможен заглавен ред по брой на колоните');
                        break;
                    }
                }
            }
        }

        if ($headerLineIndex === null) {
            $this->command->error('❌ НЕ МОГА ДА НАМЕРЯ ЗАГЛАВИЯТА НА КОЛОНИТЕ');
            $this->command->info('🔍 Първите 5 реда:');
            foreach (array_slice($lines, 0, 5) as $i => $line) {
                $this->command->info("   [{$i}]: " . substr(trim($line), 0, 100));
            }
            return;
        }

        $this->command->info('✅ Намерени са заглавния ред на ред ' . ($headerLineIndex + 1));

        // Извличане на имената на колоните
        $headers = $this->extractHeaders($headerLine);
        $this->command->info('📋 Брой колони: ' . count($headers));
        $this->command->info('🔍 Колони: ' . implode(', ', $headers));

        // Взимане само на редовете с данни (след заглавния ред)
        $dataLines = array_slice($lines, $headerLineIndex + 1);

        $totalCount = 0;
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $startTime = microtime(true);
        $processedPLUs = [];

        // Обработка на всеки ред с данни
        foreach ($dataLines as $lineIndex => $line) {
            $line = trim($line);

            // Пропускаме разделителните линии
            if (preg_match('/^[\|\-\=\s]+$/', $line)) {
                continue;
            }

            // Пропускаме празни редове
            if (empty($line) || $line === '|') {
                continue;
            }

            $totalCount++;

            try {
                // Разделяне на колоните по вертикална черта
                $columns = $this->parseTableRow($line);

                // Проверка дали броят на колоните съвпада
                if (count($columns) !== count($headers)) {
                    $this->command->warn("⚠️ Ред {$totalCount}: Брой колони (" . count($columns) . ") не съвпада с брой заглавки (" . count($headers) . ")");
                    
                    if (count($columns) < count($headers)) {
                        while (count($columns) < count($headers)) {
                            $columns[] = '';
                        }
                    } else {
                        $columns = array_slice($columns, 0, count($headers));
                    }
                }

                // Създаване на асоциативен масив
                $data = [];
                foreach ($headers as $index => $header) {
                    $cleanHeader = trim($header);
                    $data[$cleanHeader] = $columns[$index] ?? '';
                }

                // Мапиране на данните от Access към нашите полета
                $oldId = $this->clean($data['PLU'] ?? '');
                
                // Пропускаме ако няма PLU
                if (empty($oldId)) {
                    $this->command->warn("⚠️ Ред {$totalCount}: Липсва PLU код, пропускам...");
                    $skippedCount++;
                    continue;
                }

                // Проверка за дублиране в текущия файл
                if (isset($processedPLUs[$oldId])) {
                    $this->command->warn("⚠️ Дублиран PLU {$oldId} във файла на ред {$totalCount}");
                    $skippedCount++;
                    continue;
                }

                $processedPLUs[$oldId] = true;

                // Проверка за дублиране в базата данни
                $existing = Product::where('old_id', $oldId)->orWhere('plu', $oldId)->first();
                if ($existing) {
                    $this->command->warn("⚠️ Пропускане на дублиран PLU в базата: " . $oldId . " - " . ($data['Name'] ?? ''));
                    $skippedCount++;
                    continue;
                }

                $productData = [
                    'old_id' => $oldId,
                    'plu' => $oldId,
                    'name' => $this->clean($data['Name'] ?? 'Продукт ' . $totalCount),
                    'code' => $oldId, // PLU става код
                    'description' => $this->clean($data['Name'] ?? ''), // Името става описание
                    'price' => $this->parseDecimal($data['Price'] ?? '0'),
                    'cost_price' => $this->parseDecimal($data['acc'] ?? '0'),
                    'quantity' => $this->parseDecimal($data['Qty'] ?? '0'),
                    'unit_of_measure' => $this->clean($data['UOM'] ?? 'бр.'),
                    'location' => null,
                    'min_stock' => 0,
                    'max_stock' => null,
                    'barcode' => $oldId, // PLU става баркод
                    'vendor_code' => null,
                    'manufacturer' => null,
                    'vat_rate' => '20%',
                    'accounting_code' => $this->clean($data['acc'] ?? ''),
                    'is_active' => true,
                    'is_service' => false,
                    'track_stock' => true,
                    'is_taxable' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                // Специален случай: ако acc е празно, задаваме себестойност
                if (empty($data['acc']) || $data['acc'] == '0') {
                    $productData['cost_price'] = $productData['price'] * 0.7; // 30% марж
                }

                // Проверка за празно име
                if (empty($productData['name']) || $productData['name'] === 'Продукт ' . $totalCount) {
                    $this->command->warn("⚠️ Ред {$totalCount}: Липсва име на продукт, пропускам...");
                    $skippedCount++;
                    continue;
                }

                // Създаване на продукта
                Product::create($productData);
                $importedCount++;

                // Показване на прогрес
                if ($importedCount % 100 == 0) {
                    $this->command->info("📦 Импортирани: {$importedCount} продукта...");
                }

                // Показване на примерни данни за първите 3 записа
                if ($importedCount <= 3) {
                    $this->command->info("   Пример {$importedCount}: PLU {$productData['plu']} - {$productData['name']} - Цена: {$productData['price']} лв.");
                }

            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Грешка при импорт на продукт', [
                    'row' => $totalCount,
                    'line' => $line,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                if ($errorCount <= 5) {
                    $this->command->error("❌ Грешка при ред {$totalCount}: " . $e->getMessage());
                }
            }
        }

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        // Извеждане на резултати
        $this->command->info('========================================');
        $this->command->info('📊 РЕЗУЛТАТИ ОТ ИМПОРТА НА ПРОДУКТИ:');
        $this->command->info('========================================');
        $this->command->info("✅ УСПЕШНО ИМПОРТИРАНИ: {$importedCount} продукта");
        $this->command->info("📝 ОБЩО РЕДОВЕ В TXT: {$totalCount}");
        $this->command->info("⏭️  ПРОПУСНАТИ (дубликати/празни): {$skippedCount}");
        $this->command->info("❌ ГРЕШКИ: {$errorCount}");
        $this->command->info("⏱️  ВРЕМЕ ЗА ИЗПЪЛНЕНИЕ: {$executionTime} секунди");

        if ($errorCount > 0) {
            $this->command->warn("⚠️  Има грешки при импорта. Проверете laravel.log за повече детайли.");
        }

        // Допълнителна статистика
        $activeProducts = Product::where('is_active', true)->count();
        $services = Product::where('is_service', true)->count();
        
        // Изчисляване на стойността на наличността
        $totalStockValue = Product::where('track_stock', true)
            ->get()
            ->sum(function($product) {
                return $product->quantity * $product->cost_price;
            });

        $this->command->info('========================================');
        $this->command->info('📈 СТАТИСТИКА СЛЕД ИМПОРТ:');
        $this->command->info('========================================');
        $this->command->info("📦 ОБЩО ПРОДУКТИ В БАЗАТА: " . Product::count());
        $this->command->info("✅ АКТИВНИ ПРОДУКТИ: {$activeProducts}");
        $this->command->info("🔧 УСЛУГИ: {$services}");
        $this->command->info("💰 СТОЙНОСТ НА НАЛИЧНОСТТА: " . number_format($totalStockValue, 2) . " лв.");
        $this->command->info('========================================');

        // Съвет за следващи стъпки
        if ($importedCount > 0) {
            $this->command->info('🎉 ИМПОРТЪТ НА ПРОДУКТИ ЗАВЪРШИ УСПЕШНО!');
            $this->command->info('➡️  Следваща стъпка: Проверете данните и при необходимост актуализирайте местоположения и допълнителна информация');
        }
    }

    /**
     * Извлича имената на колоните от заглавния ред
     */
    private function extractHeaders(string $headerLine): array
    {
        // Премахваме първия и последния символ '|' ако съществуват
        $headerLine = trim($headerLine, '| ');
        
        // Разделяме по '|'
        $parts = explode('|', $headerLine);
        
        // Почистваме всяка част
        $headers = array_map(function($part) {
            return trim($part);
        }, $parts);
        
        // Премахваме празните елементи
        $headers = array_filter($headers, function($header) {
            return !empty($header);
        });
        
        return array_values($headers);
    }

    /**
     * Парсва ред от таблицата
     */
    private function parseTableRow(string $line): array
    {
        // Премахваме първия и последния символ '|' ако съществуват
        $line = trim($line, '| ');
        
        // Разделяме по '|', но внимаваме за празни полета
        $parts = explode('|', $line);
        
        // Почистваме всяка част
        $columns = array_map(function($part) {
            return trim($part);
        }, $parts);
        
        return $columns;
    }

    /**
     * Почистване на низ
     */
    private function clean($string): string
    {
        if (!is_string($string)) {
            return '';
        }
        
        $string = trim($string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
        return $string;
    }

    /**
     * Парсване на десетично число с български формат (запетая като десетичен разделител)
     */
    private function parseDecimal($value): float
    {
        if (empty($value)) {
            return 0.0;
        }
        
        $value = trim((string)$value);
        
        // Премахваме всички интервали
        $value = preg_replace('/\s+/', '', $value);
        
        // Заменяме запетая с точка за десетичен разделител
        $value = str_replace(',', '.', $value);
        
        // Премахваме всички символи, които не са цифри, точка или минус
        $value = preg_replace('/[^\d\.\-]/', '', $value);
        
        return (float) $value;
    }
}