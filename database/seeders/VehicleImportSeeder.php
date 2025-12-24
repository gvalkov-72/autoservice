<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
     * Парсване на ASCII табличен формат - ТОЧНА ВЕРСИЯ
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
            if (str_contains($line, 'Поръчка') || 
                str_contains($line, 'Клиент') ||
                str_contains($line, 'PODate')) {
                continue;
            }
            
            // Разделяне на колони по вертикални линии
            $columns = explode('|', $line);
            
            // Премахване на първия и последния празен елемент
            if (count($columns) > 2) {
                array_shift($columns); // премахване на първия празен
                array_pop($columns);   // премахване на последния празен
            }
            
            // Почистване на колоните (премахване на излишни интервали)
            $columns = array_map('trim', $columns);
            
            // Очакваме 12 колони според структурата
            if (count($columns) >= 12) {
                $tableData[] = [
                    'order_reference' => $columns[0] ?? '',      // Поръчка (0, 1, 2...)
                    'customer_name'   => $columns[1] ?? '',      // Клиент (ПЕТЪР КИРИЛОВ, Иво...)
                    'po_date'         => $columns[2] ?? '',      // PODate (07.12.2017 ?.)
                    'author'          => $columns[3] ?? '',      // Author (ЕМИЛ БОГОЕВ)
                    'notes'           => $columns[4] ?? '',      // Забележка (празно)
                    'chassis'         => $columns[5] ?? '',      // Шаси (72368449)
                    'phone'           => $columns[6] ?? '',      // Телефон (0888525030)
                    'vehicle_name'    => $columns[7] ?? '',      // Автомобил (КСАНТИЯ)
                    'plate'           => $columns[8] ?? '',      // ДК No (CA1358PC)
                    'monitor_code'    => $columns[9] ?? '',      // Код на монитора (4)
                    'mileage'         => $columns[10] ?? '',     // Изминати км (294200)
                    'service_amt'     => $columns[11] ?? '',     // serviceamt (0)
                ];
            }
        }
        
        return $tableData;
    }

    /**
     * Търсене на клиент по име - ПО-ГЪВКАВ МЕТОД
     */
    private function findCustomerByName(string $customerName): ?Customer
    {
        $customerName = $this->fixAccessEncoding($customerName);
        
        if (empty($customerName)) {
            return null;
        }

        // 1. Точен match
        $customer = Customer::where('name', $customerName)->first();
        if ($customer) {
            return $customer;
        }

        // 2. LIKE търсене
        $customer = Customer::where('name', 'LIKE', "%{$customerName}%")->first();
        if ($customer) {
            return $customer;
        }

        // 3. Търсене по първо име (ако има пълно име)
        $nameParts = explode(' ', $customerName);
        if (count($nameParts) > 1) {
            $firstName = $nameParts[0];
            $customer = Customer::where('name', 'LIKE', "%{$firstName}%")->first();
            if ($customer) {
                return $customer;
            }
        }

        // 4. Търсене без интервали
        $cleanName = preg_replace('/\s+/', '', $customerName);
        $customers = Customer::all();
        
        foreach ($customers as $c) {
            $cleanCustomerName = preg_replace('/\s+/', '', $c->name);
            if (strcasecmp($cleanName, $cleanCustomerName) === 0) {
                return $c;
            }
        }

        return null;
    }

    /**
     * Изпълнява импортирането на превозни средства
     */
    public function run(): void
    {
        $this->command->info('🚗 СТАРТИРАНЕ НА ИМПОРТ НА ПРЕВОЗНИ СРЕДСТВА...');
        $this->command->line(str_repeat('═', 70));

        // Път към файла
        $filePath = base_path('old-database/Vehicle.txt');
        
        if (!file_exists($filePath)) {
            $this->command->error("❌ Файлът не е намерен: $filePath");
            return;
        }
        
        // Прочитане на файла
        $content = file_get_contents($filePath);
        $this->command->info("📁 Файл: " . basename($filePath));
        $this->command->info("📊 Размер: " . round(strlen($content) / 1024, 2) . " KB");
        
        // ПАРСВАНЕ НА ТАБЛИЧНИЯ ФОРМАТ
        $this->command->info("\n📋 ПАРСВАНЕ НА ASCII ТАБЛИЦАТА...");
        $tableData = $this->parseTableFormat($content);
        
        if (empty($tableData)) {
            $this->command->error('❌ Не мога да извлека данни от таблицата!');
            return;
        }
        
        $this->command->info("✅ Успешно извлечени " . count($tableData) . " реда");
        
        // ДЕБЪГ: Покажи първите 3 реда КОРЕКТНО
        $this->command->info("\n🔍 ПЪРВИ 3 РЕДА (коректно парсване):");
        for ($i = 0; $i < min(3, count($tableData)); $i++) {
            $row = $tableData[$i];
            $this->command->info("Ред {$i}:");
            $this->command->info("  Поръчка: " . ($row['order_reference'] ?? ''));
            $this->command->info("  Клиент: '" . ($row['customer_name'] ?? '') . "'");
            $this->command->info("  Телефон: " . ($row['phone'] ?? ''));
            $this->command->info("  Рег. номер: " . ($row['plate'] ?? ''));
            $this->command->info("  Автомобил: " . ($row['vehicle_name'] ?? ''));
            $this->command->line("  " . str_repeat('-', 50));
        }
        
        // ПРОВЕРКА ЗА КЛИЕНТИ В БАЗАТА
        $this->command->info("\n👥 ПРОВЕРКА ЗА СЪВПАДЕНИЯ НА КЛИЕНТИ...");
        
        // Тествай първите 5 клиента дали съществуват
        $testCustomers = [];
        for ($i = 0; $i < min(5, count($tableData)); $i++) {
            $customerName = $tableData[$i]['customer_name'];
            $customerName = $this->fixAccessEncoding($customerName);
            $testCustomers[] = $customerName;
        }
        
        foreach ($testCustomers as $customerName) {
            $found = Customer::where('name', $customerName)->exists() 
                    ? '✅' 
                    : (Customer::where('name', 'LIKE', "%{$customerName}%")->exists() ? '⚠️' : '❌');
            
            $this->command->info("{$found} Клиент: '{$customerName}'");
        }
        
        // ⭐⭐⭐ ИМПОРТ НА ДАННИТЕ ⭐⭐⭐
        $this->command->info("\n⭐ ИМПОРТ НА ПРЕВОЗНИ СРЕДСТВА ⭐");
        
        $importedCount = 0;
        $skippedCount = 0;
        $totalRows = count($tableData);
        
        // Идентификатор на импортната партида
        $importBatch = 'VEHICLE_IMPORT_' . date('Ymd_His');
        
        // За статистика
        $missedCustomers = [];
        $successfulImports = [];
        
        // Използвай DB::transaction за по-бързо вмъкване
        DB::beginTransaction();
        
        try {
            foreach ($tableData as $index => $row) {
                try {
                    // Пропускане на празни редове
                    if (empty($row['customer_name']) && empty($row['plate'])) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // ФИКСИРАНЕ НА КОДИРОВКАТА
                    $customerName = $this->fixAccessEncoding($row['customer_name']);
                    $vehicleName = $this->fixAccessEncoding($row['vehicle_name']);
                    $author = $this->fixAccessEncoding($row['author']);
                    
                    // ТЪРСЕНЕ НА КЛИЕНТА
                    $customer = $this->findCustomerByName($customerName);
                    
                    if (!$customer) {
                        $missedCustomers[$customerName] = ($missedCustomers[$customerName] ?? 0) + 1;
                        $skippedCount++;
                        continue;
                    }
                    
                    // РАЗДЕЛЯНЕ НА МАРКА И МОДЕЛ
                    list($make, $model) = $this->splitMakeAndModel($vehicleName);
                    
                    // ПАРСВАНЕ НА ДАТАТА (премахване на ' ?.')
                    $poDate = null;
                    if (!empty($row['po_date'])) {
                        $dateStr = str_replace(' ?.', '', trim($row['po_date']));
                        $dateParts = explode('.', $dateStr);
                        if (count($dateParts) === 3) {
                            $poDate = \Carbon\Carbon::createFromDate($dateParts[2], $dateParts[1], $dateParts[0])->toDateString();
                        }
                    }
                    
                    // ПАРСВАНЕ НА ПРОБЕГА
                    $mileage = null;
                    if (!empty($row['mileage']) && is_numeric($row['mileage'])) {
                        $mileage = (int) $row['mileage'];
                    }
                    
                    // ПРОВЕРКА ЗА ДУБЛИКАТИ
                    $existingVehicle = Vehicle::where('old_system_id', $row['order_reference'])
                        ->orWhere('plate', $row['plate'])
                        ->first();
                    
                    if ($existingVehicle) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // ПОДГОТОВКА НА ДАННИТЕ
                    $vehicleData = [
                        'customer_id'     => $customer->id,
                        'old_system_id'   => $row['order_reference'] ?: null,
                        'import_batch'    => $importBatch,
                        'chassis'         => $row['chassis'] ?: null,
                        'vin'             => $row['chassis'] ?: null, // Шаси е VIN
                        'plate'           => $row['plate'] ?: null,
                        'make'            => $make ?: 'Unknown',
                        'model'           => $model ?: '',
                        'mileage'         => $mileage,
                        'dk_no'           => $row['plate'] ?: null,
                        'monitor_code'    => $row['monitor_code'] ?: null,
                        'order_reference' => $row['order_reference'] ?: null,
                        'po_date'         => $poDate,
                        'author'          => $author ?: null,
                        'notes'           => $row['notes'] ?: null,
                        'is_active'       => true,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                    
                    // Създаване на записа
                    DB::table('vehicles')->insert($vehicleData);
                    $importedCount++;
                    
                    // Запомни успешните импорти за показване
                    if (count($successfulImports) < 5) {
                        $successfulImports[] = [
                            'plate' => $row['plate'],
                            'customer' => $customer->name,
                            'vehicle' => $vehicleName
                        ];
                    }
                    
                    if ($importedCount % 100 === 0) {
                        $this->command->info("  ✅ Импортирани {$importedCount} превозни средства...");
                    }
                    
                } catch (\Exception $e) {
                    Log::error("VehicleImport: Грешка на ред {$index}", [
                        'row_data' => $row,
                        'error' => $e->getMessage(),
                    ]);
                    $skippedCount++;
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Грешка при импорта: " . $e->getMessage());
            return;
        }
        
        // ⭐⭐⭐ РЕЗЮМЕ НА ИМПОРТА ⭐⭐⭐
        $this->command->line(str_repeat('═', 70));
        $this->command->info('📊 РЕЗЮМЕ НА ИМПОРТА НА ПРЕВОЗНИ СРЕДСТВА');
        $this->command->line(str_repeat('─', 70));
        $this->command->info("   Импортирани: {$importedCount} превозни средства");
        $this->command->info("   Пропуснати:  {$skippedCount} записа");
        $this->command->info("   Общо редове: {$totalRows}");
        $this->command->info("   Импортна партида: {$importBatch}");
        
        // Покажи няколко успешни импорта
        if (!empty($successfulImports)) {
            $this->command->info("\n✅ УСПЕШНИ ИМПОРТИ (пример):");
            foreach ($successfulImports as $import) {
                $this->command->info("   🚗 {$import['plate']} - {$import['vehicle']} за '{$import['customer']}'");
            }
        }
        
        // СТАТИСТИКА ЗА ПРОПУСНАТИТЕ КЛИЕНТИ
        if (!empty($missedCustomers)) {
            $this->command->warn("\n⚠️  ПРОПУСНАТИ КЛИЕНТИ (топ 10):");
            arsort($missedCustomers);
            $topMissed = array_slice($missedCustomers, 0, 10, true);
            
            foreach ($topMissed as $customerName => $count) {
                $this->command->line("   - '{$customerName}': {$count} пъти");
            }
            
            $totalMissed = array_sum($missedCustomers);
            $this->command->info("\n📈 Общо пропуснати клиенти: {$totalMissed} от {$skippedCount} пропуснати записи");
            
            // ДИРЕКТНА ПРОВЕРКА: Дай пример за един клиент който не се намира
            if (!empty($missedCustomers)) {
                $exampleCustomer = array_key_first($missedCustomers);
                $this->command->info("\n🔍 ПРИМЕР ЗА ПРОВЕРКА:");
                $this->command->info("   Търся клиент: '{$exampleCustomer}'");
                
                // Проверка в базата
                $foundExact = Customer::where('name', $exampleCustomer)->exists();
                $foundLike = Customer::where('name', 'LIKE', "%{$exampleCustomer}%")->exists();
                
                $this->command->info("   Точно съвпадение: " . ($foundExact ? '✅' : '❌'));
                $this->command->info("   Частично съвпадение: " . ($foundLike ? '✅' : '❌'));
                
                if (!$foundExact && !$foundLike) {
                    $this->command->info("\n💡 Проблемът е, че клиентите от Vehicle.txt не съвпадат с тези в базата!");
                    $this->command->info("   Може да има разлики в имената или клиентите да не са импортирани.");
                }
            }
        }
        
        $this->command->line(str_repeat('═', 70));
    }
}