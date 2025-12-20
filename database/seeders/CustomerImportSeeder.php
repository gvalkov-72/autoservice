<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CustomerImportSeeder extends Seeder
{
    /**
     * Конвертира Access Mojibake текст към правилна кирилица
     * Специално за Access българска кирилица
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
     * Парсва табличен текст формат с пајпове
     * Формат: | Number | Customer-Name | Customer-Address-1 | ...
     */
    private function parseTableFormat(string $content): array
    {
        $lines = explode("\n", $content);
        $data = [];
        
        $headerLine = null;
        $headers = [];
        
        // 1. Намери заглавния ред (този с имената на колоните)
        foreach ($lines as $line) {
            if (strpos($line, '|   Number   |') !== false || 
                strpos($line, '| Number |') !== false) {
                $headerLine = $line;
                break;
            }
        }
        
        if (!$headerLine) {
            $this->command->error('❌ Не мога да намеря заглавния ред с колоните!');
            return [];
        }
        
        // 2. Извлечи заглавките
        // Премахни началния и крайния '|'
        $headerLine = trim($headerLine, "| \t\n\r\0\x0B");
        // Раздели по '|' и trim-ни всяка колона
        $rawHeaders = array_map('trim', explode('|', $headerLine));
        
        // 3. Мапирай заглавките към стандартни имена
        $headerMapping = [
            'Number' => 'Number',
            'Customer-Name' => 'Customer-Name',
            'Customer-Address-1' => 'Customer-Address-1',
            'Customer-Address-2' => 'Customer-Address-2',
            'Customer-MOL' => 'Customer-MOL',
            'Customer-Taxno' => 'Customer-Taxno',
            'Customer-DocType' => 'Customer-DocType',
            'Receiver' => 'Receiver',
            'Receiver Details' => 'Receiver Details',
            'Customer-Bulstat' => 'Customer-Bulstat',
            'Telno' => 'Telno',
            'Faxno' => 'Faxno',
            'E-mail' => 'E-mail',
            'ResAddress1' => 'ResAddress1',
            'ResAddress2' => 'ResAddress2',
            'eidate' => 'eidate',
            'include' => 'include',
            'active' => 'active',
            'customer' => 'customer',
            'supplier' => 'supplier',
            'Contact' => 'Contact',
            'partida' => 'partida',
            'bulstatletter' => 'bulstatletter',
        ];
        
        $headers = [];
        foreach ($rawHeaders as $rawHeader) {
            $normalized = trim(preg_replace('/\s+/', ' ', $rawHeader));
            // Опитай да намериш съответствие
            foreach ($headerMapping as $key => $value) {
                if (stripos($normalized, $key) !== false) {
                    $headers[] = $key;
                    break;
                }
            }
        }
        
        $this->command->info("✅ Намерени заглавки: " . count($headers));
        $this->command->info("📋 Заглавки: " . implode(', ', array_slice($headers, 0, 5)) . '...');
        
        // 4. Обработка на данните
        $inDataSection = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Пропускай разделителните редове
            if (strpos($line, '---') === 0 || strpos($line, '===') === 0) {
                if ($inDataSection) {
                    $inDataSection = false; // Край на секцията с данни
                } else {
                    $inDataSection = true; // Начало на секцията с данни
                }
                continue;
            }
            
            // Пропускай празни редове или заглавни редове
            if (empty($line) || strpos($line, '|   Number   |') !== false) {
                continue;
            }
            
            // Само редове с данни (започват с '|')
            if (strpos($line, '|') === 0 && $inDataSection) {
                // Премахни '|' в началото и края
                $line = trim($line, "| \t\n\r\0\x0B");
                
                // Раздели по '|' - важно: запази празните стойности
                $columns = explode('|', $line);
                
                // Trim-ни всяка колона
                $columns = array_map(function($col) {
                    return trim($col);
                }, $columns);
                
                // Ако имаме по-малко колони от заглавките, добави празни
                while (count($columns) < count($headers)) {
                    $columns[] = '';
                }
                
                // Ако имаме повече колони, съкрати
                $columns = array_slice($columns, 0, count($headers));
                
                // Създай асоциативен масив
                $rowData = array_combine($headers, $columns);
                
                // Добави към данните само ако има Number
                if (!empty($rowData['Number'])) {
                    $data[] = $rowData;
                }
            }
        }
        
        return $data;
    }

    public function run(): void
    {
        $this->command->info('🚀 ИМПОРТ ОТ ACCESS (ТАБЛИЧЕН ФОРМАТ)');
        $this->command->line(str_repeat('═', 70));
        
        // Път към файла
        $filePath = base_path('old-database/Customer.txt');
        
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
        
        // ПАРСВАНЕ НА ТАБЛИЧНИЯ ФОРМАТ
        $this->command->info("\n📋 ПАРСВАНЕ НА ТАБЛИЧЕН ФОРМАТ...");
        $tableData = $this->parseTableFormat($content);
        
        if (empty($tableData)) {
            $this->command->error('❌ Не мога да извлека данни от табличния формат!');
            $this->command->info('💡 Експортирай от Access като "Text File" с разделител Tab, не като "Formatted Text"');
            return;
        }
        
        $this->command->info("✅ Намерени записи: " . count($tableData));
        
        $imported = 0;
        $errors = [];
        
        $this->command->info("\n📥 ЗАПОЧВАМ ИМПОРТ...");
        $progressBar = $this->command->getOutput()->createProgressBar(count($tableData));
        $progressBar->start();
        
        // ИМПОРТ НА ДАННИТЕ
        foreach ($tableData as $index => $rowData) {
            $progressBar->advance();
            
            try {
                $customerData = $this->prepareCustomerData($rowData);
                
                // ПРОВЕРКА ЗА ЗАДЪЛЖИТЕЛНИ ПОЛЕТА
                if (empty($customerData['name'])) {
                    $errors[] = "Запис {$rowData['Number']}: Липсва име";
                    continue;
                }
                
                // СЪЗДАЙ КЛИЕНТА
                Customer::create($customerData);
                $imported++;
                
            } catch (\Exception $e) {
                $errors[] = "Запис {$rowData['Number']}: " . $e->getMessage();
                Log::error('Import error', [
                    'number' => $rowData['Number'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $progressBar->finish();
        
        // РЕЗУЛТАТИ
        $this->command->line("\n");
        $this->command->info('✅ ИМПОРТЪТ ЗАВЪРШИ');
        $this->command->line(str_repeat('═', 70));
        $this->command->info("🟢 Успешно импортирани: $imported клиенти");
        
        if (!empty($errors)) {
            $this->command->warn("🟡 Грешки: " . count($errors));
            foreach (array_slice($errors, 0, 5) as $error) {
                $this->command->line("   • $error");
            }
            if (count($errors) > 5) {
                $this->command->line("   ... и още " . (count($errors) - 5) . " грешки");
            }
        }
        
        if ($imported > 0) {
            $this->command->info("\n🎉 КЛИЕНТИТЕ СА ИМПОРТИРАНИ УСПЕШНО!");
            $this->command->info("💡 Сега можеш да продължиш с импорта на продуктите.");
        }
    }
    
    /**
     * Тестване на encoding конверсията
     */
    private function testEncodingFix(): void
    {
        $this->command->info('🧪 ТЕСТ НА КОНВЕРСИЯТА:');
        
        $testCases = [
            'Êàëîÿí Ïå÷åíÿðñêè' => 'КАЛОЯН ПЕЧЕНЯРСКИ',
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
            $resultUpper = mb_strtoupper($result, 'UTF-8');
            $expectedUpper = mb_strtoupper($expected, 'UTF-8');
            
            $isMatch = ($resultUpper === $expectedUpper);
            
            if ($isMatch) {
                $passed++;
                $this->command->line("✅ " . $this->truncate($input, 25) . 
                                   " → " . $this->truncate($result, 25));
            } else {
                $this->command->line("❌ " . $this->truncate($input, 25) . 
                                   " → " . $this->truncate($result, 25));
                $this->command->line("   Очаквано: " . $expected);
            }
        }
        
        $this->command->line(str_repeat('─', 70));
        $this->command->info("📊 Резултат: $passed от $total теста минаха успешно");
    }
    
    /**
     * Подготвя данните за клиента
     */
    private function prepareCustomerData(array $oldData): array
    {
        // Конвертирай всички текстови полета
        $converted = [];
        foreach ($oldData as $key => $value) {
            if (is_string($value)) {
                $converted[$key] = $this->fixAccessEncoding($value);
            } else {
                $converted[$key] = $value;
            }
        }
        
        return [
            'old_system_id'       => $converted['Number'] ?? null,
            'type'                => $this->determineType($converted),
            'name'                => trim($converted['Customer-Name'] ?? ''),
            'vat_number'          => $this->cleanVatNumber($converted['Customer-Taxno'] ?? ''),
            'bulstat'             => trim($converted['Customer-Bulstat'] ?? ''),
            'contact_person'      => trim($converted['Customer-MOL'] ?? ''),
            'phone'               => $this->cleanPhone($converted['Telno'] ?? ''),
            'fax'                 => trim($converted['Faxno'] ?? ''),
            'email'               => $this->cleanEmail($converted['E-mail'] ?? ''),
            'address'             => $this->formatAddress($converted),
            'address_line1'       => trim($converted['Customer-Address-1'] ?? ''),
            'address_line2'       => trim($converted['Customer-Address-2'] ?? ''),
            'city'                => $this->extractCity($converted),
            'notes'               => $this->formatNotes($converted),
            'court_registration'  => trim($converted['partida'] ?? ''),
            'bulstat_letter'      => trim($converted['bulstatletter'] ?? ''),
            'is_active'           => $this->parseBoolean($converted['active'] ?? ''),
            'include_in_reports'  => $this->parseBoolean($converted['include'] ?? ''),
            'created_at'          => $this->parseDate($converted['eidate'] ?? ''),
        ];
    }
    
    private function determineType(array $data): string
    {
        $isCustomer = isset($data['customer']) && strtoupper(trim($data['customer'])) === 'ДА';
        $isSupplier = isset($data['supplier']) && strtoupper(trim($data['supplier'])) === 'ДА';
        
        if ($isCustomer && $isSupplier) return 'both';
        if ($isSupplier) return 'supplier';
        return 'customer';
    }
    
    private function cleanVatNumber(string $vat): ?string
    {
        $vat = trim($vat);
        if (empty($vat)) return null;
        
        $vat = preg_replace('/\s+/', '', $vat);
        if (!str_starts_with(strtoupper($vat), 'BG')) {
            $vat = 'BG' . $vat;
        }
        
        return $vat;
    }
    
    private function cleanPhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($phone));
        return !empty($phone) ? $phone : null;
    }
    
    private function cleanEmail(string $email): ?string
    {
        $email = trim($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }
    
    private function formatAddress(array $data): string
    {
        $parts = [];
        if (!empty($data['Customer-Address-1'])) $parts[] = trim($data['Customer-Address-1']);
        if (!empty($data['Customer-Address-2'])) $parts[] = trim($data['Customer-Address-2']);
        return implode(', ', $parts);
    }
    
    private function extractCity(array $data): string
    {
        $address = $data['Customer-Address-1'] ?? '';
        $address = mb_strtoupper($address, 'UTF-8');
        
        if (str_contains($address, 'СОФИЯ')) return 'София';
        if (str_contains($address, 'ПЛОВДИВ')) return 'Пловдив';
        if (str_contains($address, 'ВАРНА')) return 'Варна';
        if (str_contains($address, 'БУРГАС')) return 'Бургас';
        if (str_contains($address, 'РУСЕ')) return 'Русе';
        
        return 'София';
    }
    
    private function formatNotes(array $data): ?string
    {
        $notes = [];
        if (!empty($data['Receiver'])) $notes[] = 'Получател: ' . trim($data['Receiver']);
        if (!empty($data['Contact'])) $notes[] = 'Контакт: ' . trim($data['Contact']);
        return !empty($notes) ? implode("\n", $notes) : null;
    }
    
    private function parseBoolean(string $value): bool
    {
        $value = strtoupper(trim($value));
        return $value === 'ДА';
    }
    
    private function parseDate(string $date): ?string
    {
        if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $date, $matches)) {
            $day = (int)$matches[1];
            $month = (int)$matches[2];
            $year = (int)$matches[3];
            
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
        return now()->toDateTimeString();
    }
    
    private function truncate(string $text, int $length): string
    {
        if (mb_strlen($text, 'UTF-8') <= $length) return $text;
        return mb_substr($text, 0, $length - 3, 'UTF-8') . '...';
    }
}