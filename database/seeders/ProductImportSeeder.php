<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductImportSeeder extends Seeder
{
    /**
     * Конвертира Access Mojibake текст към правилна кирилица
     */
    private function fixAccessEncoding(string $text): string
    {
        $text = trim($text);
        if (empty($text)) return $text;

        // Ако вече е правилна кирилица, върни както е
        if (preg_match('/[А-Яа-яЁё]/u', $text)) {
            return $text;
        }

        // Основни Access-кирилица мапинг
        $accessFixMap = [
            // Главни букви
            'À' => 'А', 'Á' => 'Б', 'Â' => 'В', 'Ã' => 'Г', 'Ä' => 'Д',
            'Å' => 'Е', 'Æ' => 'Ж', 'Ç' => 'З', 'È' => 'И', 'É' => 'Й',
            'Ê' => 'К', 'Ë' => 'Л', 'Ì' => 'М', 'Í' => 'Н', 'Î' => 'О',
            'Ï' => 'П', 'Ð' => 'Р', 'Ñ' => 'С', 'Ò' => 'Т', 'Ó' => 'У',
            'Ô' => 'Ф', 'Õ' => 'Х', 'Ö' => 'Ц', '×' => 'Ч', 'Ø' => 'Ш',
            'Ù' => 'Щ', 'Ú' => 'Ъ', 'Û' => 'Ы', 'Ü' => 'Ь', 'Ý' => 'Э',
            'Þ' => 'Ю', 'ß' => 'Я',
            
            // Малки букви
            'à' => 'а', 'á' => 'б', 'â' => 'в', 'ã' => 'г', 'ä' => 'д',
            'å' => 'е', 'æ' => 'ж', 'ç' => 'з', 'è' => 'и', 'é' => 'й',
            'ê' => 'к', 'ë' => 'л', 'ì' => 'м', 'í' => 'н', 'î' => 'о',
            'ï' => 'п', 'ð' => 'р', 'ñ' => 'с', 'ò' => 'т', 'ó' => 'у',
            'ô' => 'ф', 'õ' => 'х', 'ö' => 'ц', '÷' => 'ч', 'ø' => 'ш',
            'ù' => 'щ', 'ú' => 'ъ', 'û' => 'ы', 'ü' => 'ь', 'ý' => 'э',
            'þ' => 'ю', 'ÿ' => 'я',
        ];

        return strtr($text, $accessFixMap);
    }

    /**
     * Преобразува българска десетична запетая в точка
     */
    private function parseDecimal(string $value): float
    {
        $value = trim($value);
        if (empty($value)) return 0.0;

        // Премахване на интервали и други символи
        $value = preg_replace('/[^\d,.-]/', '', $value);
        
        // Замяна на българска запетая с точка
        $value = str_replace(',', '.', $value);
        
        // Премахване на повече от една точка
        $parts = explode('.', $value);
        if (count($parts) > 2) {
            $value = $parts[0] . '.' . $parts[1];
        }

        return (float) $value;
    }

    /**
     * Парсва ред от табличния файл
     */
    private function parseTableLine(string $line): ?array
    {
        $line = trim($line);
        
        // Пропускаме разделителните редове и празни редове
        if (empty($line) || preg_match('/^[-|=]+$/', $line) || str_starts_with($line, '|')) {
            return null;
        }

        // Пропускаме заглавния ред
        if (str_contains($line, 'PLU') || str_contains($line, 'Name') || 
            str_contains($line, 'UOM') || str_contains($line, 'Qty') || 
            str_contains($line, 'Price') || str_contains($line, 'acc')) {
            return null;
        }

        // Разделяне по вертикални линии
        $columns = explode('|', $line);
        
        // Премахване на първия и последния празен елемент
        if (count($columns) > 2) {
            array_shift($columns);
            array_pop($columns);
        }

        // Почистване на колоните
        $columns = array_map('trim', $columns);

        // Очакваме 6 колони: PLU, Name, UOM, Qty, Price, acc
        if (count($columns) >= 6) {
            return [
                'PLU' => $columns[0] ?? '',
                'Name' => $columns[1] ?? '',
                'UOM' => $columns[2] ?? '',
                'Qty' => $columns[3] ?? '',
                'Price' => $columns[4] ?? '',
                'acc' => $columns[5] ?? '',
            ];
        }

        return null;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 ИМПОРТ НА ПРОДУКТИ ОТ ACCESS ФАЙЛ');
        $this->command->info('========================================');
        
        $filePath = base_path('old-database/Products.txt');
        
        // Проверка за файл
        if (!file_exists($filePath)) {
            $this->command->error('❌ ФАЙЛЪТ НЕ Е НАМЕРЕН: Products.txt');
            $this->command->info('📂 Моля поставете Products.txt в папка: old-database/');
            $this->command->info('💡 Файлът трябва да е експортиран от Access като текст с табличен формат');
            $this->command->info('   Очакван формат:');
            $this->command->info('   -----------------------------------------------------------------');
            $this->command->info('   |  PLU   |       Name       | UOM | Qty |  Price  |   acc   |');
            $this->command->info('   -----------------------------------------------------------------');
            $this->command->info('   |  16172 | накладки         | бр. |   1 |   32,40 |    7021 |');
            $this->command->info('   -----------------------------------------------------------------');
            return;
        }
        
        $this->command->info('✅ Файлът е намерен: ' . $filePath);
        $this->command->info('📖 Четене на таблични данни...');
        
        // Прочитане на целия файл
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        $totalCount = 0;
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $duplicateCount = 0;
        
        $startTime = microtime(true);
        
        // Масив за проследяване на дублирани PLU кодове
        $processedPLUs = [];
        
        foreach ($lines as $lineNumber => $line) {
            $totalCount++;
            
            // Парсване на реда
            $productData = $this->parseTableLine($line);
            
            if (!$productData) {
                $skippedCount++;
                continue;
            }
            
            try {
                // Корекция на кодирането
                $productName = $this->fixAccessEncoding($productData['Name']);
                $productUOM = $this->fixAccessEncoding($productData['UOM']);
                
                // Парсване на числовите стойности
                $quantity = (int) preg_replace('/[^\d]/', '', $productData['Qty']);
                $price = $this->parseDecimal($productData['Price']);
                $costPrice = $this->parseDecimal($productData['acc']);
                
                // Ако цената е 0, да се използва себестойността с марж
                if ($price == 0 && $costPrice > 0) {
                    $price = $costPrice * 1.3; // 30% марж по подразбиране
                }
                
                // Проверка за валиден PLU
                $oldId = trim($productData['PLU']);
                if (empty($oldId) || $oldId === 'NULL' || $oldId === '0') {
                    $this->command->warn("⚠️  Пропускане на ред {$lineNumber}: Невалиден PLU код '{$oldId}'");
                    $skippedCount++;
                    continue;
                }
                
                // Проверка за дублиране на PLU в текущия файл
                if (isset($processedPLUs[$oldId])) {
                    $this->command->warn("⚠️  Дублиран PLU {$oldId} на ред {$lineNumber}: '{$productName}'");
                    $this->command->info("   Първо срещнат като: '{$processedPLUs[$oldId]}'");
                    $duplicateCount++;
                    
                    // При дублиран PLU, добавяме суфикс
                    $oldId = $oldId . '_' . ($duplicateCount + 1);
                } else {
                    $processedPLUs[$oldId] = $productName;
                }
                
                // Проверка за дублиране в базата данни
                $existingProduct = Product::where('old_id', $oldId)->first();
                if ($existingProduct) {
                    $this->command->info("ℹ️  Продукт с PLU {$oldId} вече съществува: '{$existingProduct->name}'");
                    $skippedCount++;
                    continue;
                }
                
                // Подготовка на данните за вмъкване
                $productToInsert = [
                    'old_id' => $oldId,
                    'product_number' => $oldId, // Използваме PLU като номер на продукт
                    'sku' => 'PLU_' . $oldId,
                    'name' => $productName ?: 'Продукт ' . $oldId,
                    'unit' => $productUOM ?: 'бр.',
                    'uom_code' => $productUOM,
                    'quantity' => $quantity,
                    'price' => $price,
                    'cost_price' => $costPrice > 0 ? $costPrice : ($price * 0.7), // Ако няма себестойност, изчисляваме
                    'stock_quantity' => $quantity,
                    'vat_percent' => 20.00,
                    'min_stock_level' => $quantity > 0 ? max(1, (int)($quantity * 0.1)) : 0,
                    'reorder_level' => $quantity > 0 ? max(1, (int)($quantity * 0.2)) : 0,
                    'is_active' => true,
                    'is_service' => false,
                    'track_inventory' => $quantity > 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                
                // Добавяме brand, ако има в името марка
                if (preg_match('/(bosch|valeo|brembo|continental|goodyear|michelin|castrol|mobil|shell)/i', $productName, $matches)) {
                    $productToInsert['brand'] = ucfirst(strtolower($matches[1]));
                }
                
                // Добавяме описание според ключови думи в името
                if (preg_match('/(накладк|диск|спирач|амортисьор|филтър|масл|свещ|аккумулятор|гум)/ui', $productName, $matches)) {
                    $productToInsert['description'] = 'Авточасти - ' . $matches[1];
                }
                
                // Създаване на продукта
                Product::create($productToInsert);
                $importedCount++;
                
                // Показване на прогрес
                if ($importedCount % 50 == 0) {
                    $this->command->info("📦 Импортирани: {$importedCount} продукта...");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Грешка при импорт на продукт', [
                    'line' => $lineNumber,
                    'data' => $productData,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($errorCount <= 3) {
                    $this->command->error("❌ Грешка при ред {$lineNumber}: " . $e->getMessage());
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
        $this->command->info("📝 ОБЩО РЕДОВЕ ВЪВ ФАЙЛА: {$totalCount}");
        $this->command->info("⏭️  ПРОПУСНАТИ (разделители/заглавия): {$skippedCount}");
        $this->command->info("🔄 ДУБЛИРАНИ PLU КОДОВЕ: {$duplicateCount}");
        $this->command->info("❌ ГРЕШКИ: {$errorCount}");
        $this->command->info("⏱️  ВРЕМЕ ЗА ИЗПЪЛНЕНИЕ: {$executionTime} секунди");
        
        if ($errorCount > 0) {
            $this->command->warn("⚠️  Има грешки при импорта. Проверете laravel.log за повече детайли.");
        }
        
        if ($duplicateCount > 0) {
            $this->command->warn("⚠️  Има дублирани PLU кодове във файла. Те са обработени със суфикси.");
        }
        
        // Допълнителна статистика
        $activeProducts = Product::where('is_active', true)->count();
        $services = Product::where('is_service', true)->count();
        $totalStockValue = Product::where('track_inventory', true)
                                  ->get()
                                  ->sum(function($product) {
                                      return $product->stock_quantity * $product->cost_price;
                                  });
        $lowStockProducts = Product::where('is_active', true)
                                   ->where('track_inventory', true)
                                   ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                                   ->where('stock_quantity', '>', 0)
                                   ->count();
        
        $this->command->info('========================================');
        $this->command->info('📈 СТАТИСТИКА СЛЕД ИМПОРТ:');
        $this->command->info('========================================');
        $this->command->info("📦 ОБЩО ПРОДУКТИ В БАЗАТА: " . Product::count());
        $this->command->info("✅ АКТИВНИ ПРОДУКТИ: {$activeProducts}");
        $this->command->info("🛠️  УСЛУГИ: {$services}");
        $this->command->info("💰 СТОЙНОСТ НА НАЛИЧНОСТТА: " . number_format($totalStockValue, 2) . " лв.");
        $this->command->info("⚠️  ПРОДУКТИ С НИСКИ НАЛИЧНОСТИ: {$lowStockProducts}");
        
        // Изчисляване на средни цени
        $avgPrice = Product::avg('price') ?? 0;
        $avgCost = Product::avg('cost_price') ?? 0;
        
        $this->command->info("📊 СРЕДНА ЦЕНА: " . number_format($avgPrice, 2) . " лв.");
        $this->command->info("📊 СРЕДНА СЕБЕСТОЙНОСТ: " . number_format($avgCost, 2) . " лв.");
        
        // Примерни импортирани продукти
        $this->command->info('========================================');
        $this->command->info('🎯 ПРИМЕРНИ ИМПОРТИРАНИ ПРОДУКТИ:');
        $this->command->info('========================================');
        
        $sampleProducts = Product::latest()->take(5)->get(['old_id', 'name', 'unit', 'price', 'stock_quantity']);
        foreach ($sampleProducts as $index => $product) {
            $this->command->info(sprintf(
                "%-8s | %-30s | %-5s | %8.2f лв. | %3d бр.",
                $product->old_id,
                mb_substr($product->name, 0, 30),
                $product->unit,
                $product->price,
                $product->stock_quantity
            ));
        }
        
        $this->command->info('========================================');
        
        if ($importedCount > 0) {
            $this->command->info('🎉 ИМПОРТЪТ ЗАВЪРШИ УСПЕШНО!');
            $this->command->info('➡️  Следваща стъпка: Импорт на фактури (invoices)');
        } else {
            $this->command->error('❌ НИЩО НЕ Е ИМПОРТИРАНО! Проверете формата на файла.');
            $this->command->info('💡 Съвет: Файлът трябва да има табличен формат като в примера по-горе.');
        }
    }
}