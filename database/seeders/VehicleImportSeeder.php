<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class VehicleImportSeeder extends Seeder
{
    /**
     * Разделя името на превозното средство на марка и модел
     */
    private function splitMakeAndModel(string $vehicleName): array
    {
        $vehicleName = trim($vehicleName);
        if (empty($vehicleName)) {
            return ['', ''];
        }

        // Ако името е само една дума, приемаме че е марка
        if (!str_contains($vehicleName, ' ')) {
            return [$vehicleName, ''];
        }

        // Разделяне на първата дума (марка) и останалите (модел)
        $parts = explode(' ', $vehicleName, 2);
        return [$parts[0], $parts[1] ?? ''];
    }

    /**
     * Тестване на разделянето на марка и модел
     */
    private function testMakeModelSplit(): void
    {
        $this->command->info('🧪 ТЕСТ НА РАЗДЕЛЯНЕТО НА МАРКА И МОДЕЛ:');

        $testCases = [
            'OPEL ASTRA' => ['OPEL', 'ASTRA'],
            'BMW X5' => ['BMW', 'X5'],
            'MERCEDES-BENZ C220' => ['MERCEDES-BENZ', 'C220'],
            'AUDI' => ['AUDI', ''],
            'VW GOLF 7' => ['VW', 'GOLF 7'],
            'ФОРД ФОКУС' => ['ФОРД', 'ФОКУС'],
        ];

        $passed = 0;
        $total = count($testCases);

        $this->command->line("📋 Тестови случаи ($total общо):");

        foreach ($testCases as $input => $expected) {
            $result = $this->splitMakeAndModel($input);
            $isMatch = ($result[0] === $expected[0] && $result[1] === $expected[1]);

            if ($isMatch) {
                $passed++;
                $this->command->line("✅ " . $this->truncate($input, 20) . 
                                   " → Марка: '{$result[0]}', Модел: '{$result[1]}'");
            } else {
                $this->command->line("❌ " . $this->truncate($input, 20) . 
                                   " → Марка: '{$result[0]}', Модел: '{$result[1]}' (очаквано: '{$expected[0]}', '{$expected[1]}')");
            }
        }

        $this->command->line(str_repeat('─', 70));
        $percentage = round(($passed / $total) * 100, 1);
        $this->command->info("📊 Резултат: $passed от $total теста минаха успешно ($percentage%)");

        if ($passed < $total * 0.8) {
            $this->command->warn("⚠️  Има значителни разминавания в разделянето!");
            $this->command->info("💡 Можеш да коригираш правилата в метода splitMakeAndModel()");
        }
    }

    /**
     * Конвертира Access Mojibake текст към правилна кирилица
     * Същата функция като в CustomerImportSeeder
     */
    private function fixAccessEncoding(string $text): string
    {
        $text = trim($text);
        if (empty($text)) return $text;

        // Ако вече е правилна кирилица, върни както е
        if (preg_match('/[А-Яа-яЁё]/u', $text)) {
            return $text;
        }

        // ПОПЪЛНЕН МАПИНГ за точна конверсия
        $accessFixMap = [
            // Основни букви
            'Ê' => 'К', 'à' => 'а', 'ë' => 'л', 'î' => 'о', 'ÿ' => 'я',
            'á' => 'н', 'Ï' => 'П', 'å' => 'е', '÷' => 'ч', 'í' => 'и',
            'ð' => 'р', 'ñ' => 'с', 'è' => 'и',

            // Главни букви
            'Ø' => 'Ш', 'À' => 'А', 'Ò' => 'Т', 'Ð' => 'Р', 'Î' => 'О',
            'Ì' => 'М', 'Å' => 'Е', 'Õ' => 'Х', 'Ô' => 'Ф', 'Ö' => 'Ц',
            '×' => 'Ч', 'Ù' => 'Щ', 'Ú' => 'Ъ', 'Ü' => 'Ь', 'Ý' => 'Э',
            'Þ' => 'Ю', 'ß' => 'Я', 'Ç' => 'З', 'È' => 'И', 'É' => 'Й',
            'Ë' => 'Л', 'Í' => 'Н', 'Ñ' => 'С', 'Ó' => 'У', 'Â' => 'В',
            'Ã' => 'Г', 'Ä' => 'Д', 'Æ' => 'Ж', 'Á' => 'Б',

            // Малки букви
            'ú' => 'ъ', 'û' => 'ы', 'ü' => 'ь', 'ý' => 'э', 'þ' => 'ю',
            'ó' => 'у', 'ò' => 'т', 'õ' => 'х', 'ô' => 'ф', 'ö' => 'ц',
            'æ' => 'ж', 'ç' => 'з', 'é' => 'й', 'ê' => 'к', 'ì' => 'м',
            'ï' => 'п', 'â' => 'в', 'ã' => 'г', 'ä' => 'д', 'å' => 'е',
            'á' => 'б', 'ò' => 'т', 'õ' => 'х',

            // Специфични за "Бизнес" и "Минчев"
            'è' => 'и', 'ñ' => 'с', // за "Бизнес"
            'é' => 'н', 'â' => 'в', // за "Минчев" - 'é' трябва да е 'н', не 'й'
        ];

        $fixed = strtr($text, $accessFixMap);

        // СПЕЦИАЛНИ ПОПРАВКИ
        $fixed = preg_replace('/Калояи/u', 'КалоЯн', $fixed);
        $fixed = preg_replace('/Печеиярски/u', 'Печенярски', $fixed);
        $fixed = preg_replace('/Стефаи/u', 'Стефан', $fixed);
        $fixed = preg_replace('/Миичев/u', 'Минчев', $fixed);
        $fixed = preg_replace('/Бизиес/u', 'Бизнес', $fixed);
        $fixed = preg_replace('/ШАТРОМ  ЕООД/u', 'ШАТРОМ ЕООД', $fixed);

        return $fixed;
    }

    /**
     * Тестване на encoding конверсията
     */
    private function testEncodingFix(): void
    {
        $this->command->info('🧪 ТЕСТ НА КОНВЕРСИЯТА:');

        $testCases = [
            'Êàëîÿí Ïå÷åíÿðñêè' => 'КалоЯн Печенярски', // Променено от 'КАЛОЯН ПЕЧЕНЯРСКИ' на 'КалоЯн Печенярски'
            'ØÀÒÐÎÌ  ÅÎÎÄ' => 'ШАТРОМ ЕООД',
            'ÒÅÐÇÈÄ ÅÎÎÄ' => 'ТЕРЗИД ЕООД',
            'Å.Ò.Å. ÅÎÎÄ' => 'Е.Т.Е. ЕООД',
            'ËÈÍÄÍÅÐ ÁÚËÃÀÐÈß ÅÎÎÄ' => 'ЛИНДНЕР БЪЛГАРИЯ ЕООД',
            'Ñòåôàí Ìèí÷åâ' => 'Стефан Минчев',
            'óë. " Áèçíåñ Ïàðê Ñîôèÿ "' => 'ул. " Бизнес Парк София "',
            'Áèçíåñ' => 'Бизнес',
            'Ïàðê' => 'Парк',
            'Ñîôèÿ' => 'София',
        ];

        $passed = 0;
        $total = count($testCases);

        foreach ($testCases as $input => $expected) {
            $result = $this->fixAccessEncoding($input);
            $isMatch = ($result === $expected);

            if ($isMatch) {
                $passed++;
                $this->command->line("✅ " . $this->truncate($input, 30) . 
                                   " → " . $result);
            } else {
                $this->command->line("❌ " . $this->truncate($input, 30) . 
                                   " → " . $result . " (очаквано: $expected)");
            }
        }

        $this->command->line(str_repeat('─', 70));
        $percentage = round(($passed / $total) * 100, 1);
        $this->command->info("📊 Резултат: $passed от $total теста минаха успешно ($percentage%)");

        if ($passed < $total * 0.8) {
            $this->command->warn("⚠️  Има значителни разминавания в конверсията!");
            $this->command->info("💡 Можеш да коригираш мапинга в метода fixAccessEncoding()");
        }
    }

    /**
     * Парсване на табличен формат от Access (ASCII таблица с вертикални линии)
     * Този метод обработва специфичната ASCII таблична структура както е в CustomerImportSeeder
     */
    private function parseTableFormat(string $content): array
    {
        $lines = explode("\n", $content);
        $tableData = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Пропускане на празни редове и хоризонтални разделители
            if (empty($line) || preg_match('/^[-|=]+$/', $line)) {
                continue;
            }
            
            // Пропускане на заглавния ред с имената на колоните
            if (str_contains($line, 'Ïîðú÷êà') || 
                str_contains($line, 'Êëèåíò') ||
                str_contains($line, 'PODate')) {
                continue;
            }
            
            // Разделяне на колони по вертикални линии, но запазвайки празните полета
            $columns = explode('|', $line);
            
            // Премахване на първия и последния елемент (празни при правилна таблица)
            if (count($columns) > 2) {
                array_shift($columns); // премахване на първия празен
                array_pop($columns);   // премахване на последния празен
            }
            
            // Почистване на колоните (премахване на излишни интервали)
            $columns = array_map(function($col) {
                return trim($col);
            }, $columns);
            
            // Очакваме минимум 11 колони според структурата
            if (count($columns) >= 11) {
                $tableData[] = [
                    'order_reference' => $columns[0] ?? '',      // Поръчка
                    'customer_name'   => $columns[1] ?? '',      // Клиент
                    'po_date'         => $columns[2] ?? '',      // PODate
                    'author'          => $columns[3] ?? '',      // Author
                    'notes'           => $columns[4] ?? '',      // Забележка
                    'chassis'         => $columns[5] ?? '',      // Шаси
                    'phone'           => $columns[6] ?? '',      // Телефон
                    'vehicle_name'    => $columns[7] ?? '',      // Автомобил
                    'plate'           => $columns[8] ?? '',      // ДК No
                    'monitor_code'    => $columns[9] ?? '',      // Код на монитора
                    'mileage'         => $columns[10] ?? '',     // Изминати км
                    'service_amt'     => $columns[11] ?? '',     // serviceamt
                ];
            } else {
                // ДЕБЪГ: Покажи какво не е наред
                if (count($columns) > 0) {
                    Log::info("VehicleImport: Непълен ред с " . count($columns) . " колони", $columns);
                }
            }
        }
        
        return $tableData;
    }

    /**
     * Изпълнява импортирането на превозни средства
     */
    public function run(): void
    {
        $this->command->info('🚗 СТАРТИРАНЕ НА ИМПОРТ НА ПРЕВОЗНИ СРЕДСТВА...');
        $this->command->line(str_repeat('═', 70));

        // Път към файла (АБСОЛЮТНО СЪЩИЯ КАТО В CustomerImportSeeder)
        $filePath = base_path('old-database/Vehicle.txt');
        
        if (!file_exists($filePath)) {
            $this->command->error("❌ Файлът не е намерен: $filePath");
            return;
        }
        
        // Прочитане на файла
        $content = file_get_contents($filePath);
        $this->command->info("📁 Файл: " . basename($filePath));
        $this->command->info("📊 Размер: " . round(strlen($content) / 1024, 2) . " KB");
        
        // ТЕСТ НА КОНВЕРСИЯТА
        $this->testEncodingFix();
        
        // ⭐⭐⭐ ТЕСТ НА РАЗДЕЛЯНЕТО НА МАРКА И МОДЕЛ ⭐⭐⭐
        $this->testMakeModelSplit();
        
        // ПАРСВАНЕ НА ТАБЛИЧНИЯ ФОРМАТ
        $this->command->info("\n📋 ПАРСВАНЕ НА ТАБЛИЧЕН ФОРМАТ...");
        $tableData = $this->parseTableFormat($content);
        
        if (empty($tableData)) {
            $this->command->error('❌ Не мога да извлека данни от табличния формат!');
            $this->command->info('💡 Провери дали файлът има същата ASCII таблична структура като Customer.txt');
            $this->command->info('   Структура трябва да бъде: | Колона1 | Колона2 | Колона3 | ... |');
            return;
        }
        
        $this->command->info("✅ Успешно извлечени " . count($tableData) . " реда от таблицата");
        $this->command->line(str_repeat('─', 70));
        
        // ⭐⭐⭐ ИМПОРТ НА ДАННИТЕ ⭐⭐⭐
        $this->command->info("\n⭐ ИМПОРТ НА ПРЕВОЗНИ СРЕДСТВА ⭐");
        
        $importedCount = 0;
        $skippedCount = 0;
        $totalRows = count($tableData);
        
        // Идентификатор на импортната партида
        $importBatch = 'VEHICLE_IMPORT_' . date('Ymd_His');
        
        foreach ($tableData as $index => $row) {
            // Пропускане на заглавния ред, ако има
            if ($index === 0 && (str_contains($row['customer_name'] ?? '', 'Клиент') || 
                                 str_contains($row['order_reference'] ?? '', 'Поръчка'))) {
                continue;
            }
            
            try {
                // 1. ФИКСИРАНЕ НА КОДИРОВКАТА
                $customerName = $this->fixAccessEncoding($row['customer_name']);
                $vehicleName = $this->fixAccessEncoding($row['vehicle_name']);
                $author = $this->fixAccessEncoding($row['author']);
                $notes = $this->fixAccessEncoding($row['notes']);
                
                // 2. ТЪРСЕНЕ НА КЛИЕНТА
                $customer = Customer::where('name', 'LIKE', "%{$customerName}%")->first();
                
                if (!$customer) {
                    $this->command->warn("  ⚠️  Ред {$index}: Клиент '{$customerName}' не е намерен. Пропускане.");
                    $skippedCount++;
                    Log::warning("VehicleImport: Клиент не намерен", ['name' => $customerName, 'row' => $index]);
                    continue;
                }
                
                // 3. РАЗДЕЛЯНЕ НА МАРКА И МОДЕЛ
                list($make, $model) = $this->splitMakeAndModel($vehicleName);
                
                // 4. ПАРСВАНЕ НА ДАТАТА
                $poDate = null;
                if (!empty($row['po_date'])) {
                    $dateStr = str_replace(' г.', '', trim($row['po_date']));
                    $dateParts = explode('.', $dateStr);
                    if (count($dateParts) === 3) {
                        $poDate = \Carbon\Carbon::createFromDate($dateParts[2], $dateParts[1], $dateParts[0])->toDateString();
                    }
                }
                
                // 5. ПАРСВАНЕ НА ПРОБЕГА
                $mileage = null;
                if (!empty($row['mileage'])) {
                    $mileage = (int) preg_replace('/[^0-9]/', '', $row['mileage']);
                }
                
                // 6. ПРОВЕРКА ЗА ДУБЛИКАТИ
                $existingVehicle = Vehicle::where('old_system_id', $row['order_reference'])
                    ->orWhere('plate', $row['plate'])
                    ->first();
                
                if ($existingVehicle) {
                    $this->command->info("  ℹ️  Ред {$index}: Превозно средство вече съществува (ID: {$existingVehicle->id}). Пропускане.");
                    $skippedCount++;
                    continue;
                }
                
                // 7. ПОДГОТОВКА НА ДАННИТЕ
                $vehicleData = [
                    'customer_id'     => $customer->id,
                    'old_system_id'   => $row['order_reference'],
                    'import_batch'    => $importBatch,
                    'chassis'         => $row['chassis'] ?: null,
                    'vin'             => $row['chassis'] ?: null, // Шаси е VIN
                    'plate'           => $row['plate'] ?: null,
                    'make'            => $make,
                    'model'           => $model,
                    'mileage'         => $mileage,
                    'dk_no'           => $row['plate'] ?: null, // ДК No е регистрационния номер
                    'monitor_code'    => $row['monitor_code'] ?: null,
                    'order_reference' => $row['order_reference'],
                    'po_date'         => $poDate,
                    'author'          => $author,
                    'notes'           => $notes,
                    'is_active'       => true,
                ];
                
                // 8. СЪЗДАВАНЕ НА ЗАПИСА
                Vehicle::create($vehicleData);
                $importedCount++;
                
                $this->command->line("  ✅ Ред {$index}: Добавено {$row['plate']} за '{$customer->name}'");
                
            } catch (\Exception $e) {
                $this->command->error("  ❌ Ред {$index}: Грешка - " . $e->getMessage());
                $skippedCount++;
                Log::error("VehicleImport: Грешка на ред {$index}", [
                    'row_data' => $row,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Показване на прогрес на всеки 50 реда
            if (($index + 1) % 50 === 0) {
                $this->command->info("    📊 Обработени " . ($index + 1) . " от {$totalRows} реда...");
            }
        }
        
        // ⭐⭐⭐ РЕЗЮМЕ НА ИМПОРТА ⭐⭐⭐
        $this->command->line(str_repeat('═', 70));
        $this->command->info('📊 РЕЗЮМЕ НА ИМПОРТА НА ПРЕВОЗНИ СРЕДСТВА');
        $this->command->line(str_repeat('─', 70));
        $this->command->info("   Импортирани: {$importedCount} превозни средства");
        $this->command->info("   Пропуснати:  {$skippedCount} записа");
        $this->command->info("   Общо редове: {$totalRows}");
        $this->command->info("   Импортна партида: {$importBatch}");
        
        if ($skippedCount > 0) {
            $this->command->warn("💡 Провери logs/laravel.log за повече детайли за пропуснатите записи.");
        }
        
        $this->command->line(str_repeat('═', 70));
    }

    /**
     * Помощна функция за съкращаване на текст
     */
    private function truncate(string $text, int $length = 25): string
    {
        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 3, 'UTF-8') . '...';
    }
}