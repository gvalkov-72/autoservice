<?php
// database/seeders/CustomerImportSeeder.php
// АКТУАЛИЗИРАН ЗА TXT ФАЙЛОВЕ С ТАБЛИЧЕН ФОРМАТ

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 СТАРТИРАНЕ НА ИМПОРТ ОТ TXT ФАЙЛ');
        $this->command->info('========================================');
        
        $filePath = base_path('old-database/customer.txt');
        
        // Проверка за файл
        if (!file_exists($filePath)) {
            $this->command->error('❌ ФАЙЛЪТ НЕ Е НАМЕРЕН: customer.txt');
            $this->command->info('📂 Моля поставете customer.txt в папка: ' . dirname($filePath));
            $this->command->info('💡 Файлът трябва да е в табличен формат с вертикални разделители "|"');
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
        
        // Премахване на празните редове
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });
        
        if (count($lines) < 3) {
            $this->command->error('❌ ФАЙЛЪТ НЕ СЪДЪРЖА ДОСТАТЪЧНО ДАННИ');
            return;
        }
        
        // Намиране на заглавния ред (този с имената на колоните)
        $headerLineIndex = null;
        $headerLine = '';
        
        foreach ($lines as $index => $line) {
            if (strpos($line, '|   Number   |') !== false || 
                strpos($line, '| Number |') !== false ||
                preg_match('/\|\s*Number\s*\|/i', $line)) {
                $headerLineIndex = $index;
                $headerLine = trim($line);
                break;
            }
        }
        
        if ($headerLineIndex === null) {
            // Ако не намерим точно "Number", търсим първия ред, който изглежда като заглавка
            foreach ($lines as $index => $line) {
                if (strpos($line, '|') !== false && substr_count($line, '|') > 3) {
                    // Проверяваме дали редът не е разделителна линия
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
        $this->command->info('🔍 Колони: ' . implode(', ', array_slice($headers, 0, 10)));
        if (count($headers) > 10) {
            $this->command->info('... и още ' . (count($headers) - 10) . ' колони');
        }
        
        // Взимане само на редовете с данни (след заглавния ред)
        $dataLines = array_slice($lines, $headerLineIndex + 1); // Започваме от следващия ред
        
        $totalCount = 0;
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        $startTime = microtime(true);
        
        // Обработка на всеки ред с данни
        foreach ($dataLines as $lineIndex => $line) {
            $line = trim($line);
            
            // Пропускаме разделителните линии (съдържащи само --- или |)
            if (strpos($line, '---') !== false && strpos($line, '|') !== false && 
                preg_match('/^[\|\-\s]+$/', $line)) {
                continue;
            }
            
            // Пропускаме празни редове или редове без данни
            if (empty($line) || $line === '|' || strlen($line) < 5) {
                continue;
            }
            
            // Проверка дали това е разделителна линия
            if (preg_match('/^[\|\-\=\s]+$/', $line)) {
                continue;
            }
            
            $totalCount++;
            
            try {
                // Разделяне на колоните по вертикална черта
                $columns = $this->parseTableRow($line);
                
                // Проверка дали броят на колоните съвпада с броя на заглавките
                if (count($columns) !== count($headers)) {
                    $this->command->warn("⚠️ Ред {$totalCount}: Брой колони (" . count($columns) . ") не съвпада с брой заглавки (" . count($headers) . ")");
                    
                    // Опитваме се да поправим като добавяме/премахваме колони
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
                    $data[$header] = $columns[$index] ?? '';
                }
                
                // Подготовка на данните за вмъкване
                $customerData = [
                    'old_id' => $this->clean($data['Number'] ?? ''),
                    'customer_number' => $this->clean($data['Number'] ?? ''),
                    'name' => $this->clean($data['Customer-Name'] ?? $data['Customer-Name'] ?? 'Нов клиент ' . $totalCount),
                    'email' => $this->validateEmail($data['E-mail'] ?? $data['Email'] ?? ''),
                    'phone' => $this->cleanPhone($data['Telno'] ?? $data['Teho'] ?? ''),
                    'fax' => $this->cleanPhone($data['Faxno'] ?? ''),
                    'address' => $this->clean($data['Customer-Address-1'] ?? $data['Customer-Address-1'] ?? ''),
                    'address_2' => $this->clean($data['Customer-Address-2'] ?? $data['Customer-Address-2'] ?? ''),
                    'res_address_1' => $this->clean($data['ResAddress1'] ?? ''),
                    'res_address_2' => $this->clean($data['ResAddress2'] ?? ''),
                    'contact_person' => $this->clean($data['Contact'] ?? $data['Customer-MOL'] ?? ''),
                    'mol' => $this->clean($data['Customer-MOL'] ?? ''),
                    'tax_number' => $this->clean($data['Customer-Taxno'] ?? ''),
                    'bulstat' => $this->clean($data['Customer-Bulstat'] ?? ''),
                    'doc_type' => $this->clean($data['Customer-DocType'] ?? ''),
                    'receiver' => $this->clean($data['Receiver'] ?? ''),
                    'receiver_details' => $this->clean($data['Receiver Details'] ?? ''),
                    'eidale' => $this->clean($data['eidate'] ?? $data['eidale'] ?? ''),
                    'include_in_mailing' => $this->parseBool($data['include'] ?? '1'),
                    'partida' => $this->clean($data['partida'] ?? ''),
                    'bulsial_letter' => $this->clean($data['bulstatletter'] ?? $data['bulsialletter'] ?? ''),
                    'is_active' => $this->parseBool($data['active'] ?? '1'),
                    'is_customer' => $this->parseBool($data['customer'] ?? '1'),
                    'is_supplier' => $this->parseBool($data['supplier'] ?? '0'),
                    'notes' => $this->clean($data['Note'] ?? ''),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                
                // Проверка за дублиране
                if (!empty($customerData['old_id'])) {
                    $existing = Customer::where('old_id', $customerData['old_id'])->first();
                    if ($existing) {
                        $this->command->warn("⚠️ Пропускане на дублиран запис: " . $customerData['old_id']);
                        $skippedCount++;
                        continue;
                    }
                }
                
                // Проверка за празни имена
                if (empty($customerData['name']) || $customerData['name'] === 'Нов клиент ' . $totalCount) {
                    $this->command->warn("⚠️ Ред {$totalCount}: Липсва име на клиент, пропускам...");
                    $skippedCount++;
                    continue;
                }
                
                // Създаване на клиента
                Customer::create($customerData);
                $importedCount++;
                
                // Показване на прогрес
                if ($importedCount % 50 == 0) {
                    $this->command->info("📦 Импортирани: {$importedCount} клиенти...");
                }
                
                // Показване на примерни данни за първите 3 записа
                if ($importedCount <= 3) {
                    $this->command->info("   Пример {$importedCount}: #{$customerData['old_id']} - {$customerData['name']}");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Грешка при импорт на клиент', [
                    'row' => $totalCount,
                    'line' => $line,
                    'error' => $e->getMessage()
                ]);
                
                if ($errorCount <= 5) {
                    $this->command->error("❌ Грешка при ред {$totalCount}: " . $e->getMessage());
                    if ($errorCount === 1) {
                        $this->command->error("   Примерен ред: " . substr($line, 0, 150));
                    }
                }
            }
        }
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        // Извеждане на резултати
        $this->command->info('========================================');
        $this->command->info('📊 РЕЗУЛТАТИ ОТ ИМПОРТА:');
        $this->command->info('========================================');
        $this->command->info("✅ УСПЕШНО ИМПОРТИРАНИ: {$importedCount} клиенти");
        $this->command->info("📝 ОБЩО РЕДОВЕ В TXT: {$totalCount}");
        $this->command->info("⏭️  ПРОПУСНАТИ (дубликати/празни): {$skippedCount}");
        $this->command->info("❌ ГРЕШКИ: {$errorCount}");
        $this->command->info("⏱️  ВРЕМЕ ЗА ИЗПЪЛНЕНИЕ: {$executionTime} секунди");
        
        if ($errorCount > 0) {
            $this->command->warn("⚠️  Има грешки при импорта. Проверете laravel.log за повече детайли.");
        }
        
        if ($importedCount === 0 && $totalCount > 0) {
            $this->command->error('🔧 ВЪЗМОЖНИ ПРОБЛЕМИ:');
            $this->command->error('   1. Несъответствие в имената на колоните');
            $this->command->error('   2. Данните са в различен формат');
            $this->command->error('   3. Липса на действителни данни в таблицата');
            
            // Показваме примерен ред за анализ
            $this->command->info('🔍 Първи ред с данни за анализ:');
            foreach ($dataLines as $line) {
                $line = trim($line);
                if (!empty($line) && !preg_match('/^[\|\-\=\s]+$/', $line) && $line !== '|') {
                    $this->command->info("   " . substr($line, 0, 200));
                    break;
                }
            }
        }
        
        // Допълнителна статистика
        $activeCustomers = Customer::where('is_active', true)->count();
        $suppliers = Customer::where('is_supplier', true)->count();
        
        $this->command->info('========================================');
        $this->command->info('📈 СТАТИСТИКА СЛЕД ИМПОРТ:');
        $this->command->info('========================================');
        $this->command->info("👥 ОБЩО КЛИЕНТИ В БАЗАТА: " . Customer::count());
        $this->command->info("✅ АКТИВНИ КЛИЕНТИ: {$activeCustomers}");
        $this->command->info("🏭 ДОСТАВЧИЦИ: {$suppliers}");
        $this->command->info('========================================');
        
        // Съвет за следващи стъпки
        if ($importedCount > 0) {
            $this->command->info('🎉 ИМПОРТЪТ ЗАВЪРШИ УСПЕШНО!');
            $this->command->info('➡️  Следваща стъпка: Проверете данните в базата');
        } else {
            $this->command->error('❌ НИЩО НЕ Е ИМПОРТИРАНО!');
        }
    }
    
    /**
     * Извлича заглавките от ред с табличен формат
     */
    private function extractHeaders($headerLine): array
    {
        // Премахваме началния и крайния "|"
        $headerLine = trim($headerLine, '| ');
        
        // Разделяме по "|"
        $parts = explode('|', $headerLine);
        
        // Почистваме всяка заглавка
        $headers = [];
        foreach ($parts as $part) {
            $header = trim($part);
            if (!empty($header)) {
                $headers[] = $header;
            }
        }
        
        return $headers;
    }
    
    /**
     * Парсва ред от таблицата
     */
    private function parseTableRow($line): array
    {
        // Премахваме началния и крайния "|"
        $line = trim($line, '| ');
        
        // Разделяме по "|", но внимаваме за празни стойности
        $columns = [];
        $currentPos = 0;
        $length = strlen($line);
        
        while ($currentPos < $length) {
            // Намираме следващия "|"
            $nextPipe = strpos($line, '|', $currentPos);
            
            if ($nextPipe === false) {
                // Последна колона
                $value = substr($line, $currentPos);
                $columns[] = trim($value);
                break;
            } else {
                // Извличаме стойността
                $value = substr($line, $currentPos, $nextPipe - $currentPos);
                $columns[] = trim($value);
                $currentPos = $nextPipe + 1;
            }
        }
        
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
        
        // Премахване на излишни интервали и специални символи
        $string = trim($string);
        $string = preg_replace('/\s+/', ' ', $string);
        
        // Опит за конвертиране на кодиране, ако е необходимо
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'auto');
        }
        
        // Премахване на въпросителни и други странни символи
        $string = str_replace(['??', '?', '  '], ['', '', ' '], $string);
        
        return $string;
    }
    
    /**
     * Почистване на телефонен номер
     */
    private function cleanPhone($phone): string
    {
        $phone = $this->clean($phone);
        if (empty($phone)) {
            return '';
        }
        
        // Запазваме само цифри, плюс и интервал
        $phone = preg_replace('/[^0-9+\s]/', '', $phone);
        return trim($phone);
    }
    
    /**
     * Валидация на имейл
     */
    private function validateEmail($email): ?string
    {
        $email = $this->clean($email);
        if (empty($email)) {
            return null;
        }
        
        // Проста валидация
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return strtolower($email);
        }
        
        return null;
    }
    
    /**
     * Парсване на булева стойност
     */
    private function parseBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_numeric($value)) {
            return (bool) intval($value);
        }
        
        $value = strtolower((string) $value);
        $value = trim($value);
        
        $trueValues = ['true', 'yes', '1', 'y', 'да', 'active', 'on', 'вкл', 'включено'];
        $falseValues = ['false', 'no', '0', 'n', 'не', 'inactive', 'off', 'изкл', 'изключено'];
        
        if (in_array($value, $trueValues)) {
            return true;
        }
        
        if (in_array($value, $falseValues)) {
            return false;
        }
        
        // Специални случаи за "??"
        if ($value === '??' || $value === '?' || $value === '') {
            return true; // По подразбиране true за неизвестни стойности
        }
        
        // По подразбиране
        return !empty($value);
    }
}