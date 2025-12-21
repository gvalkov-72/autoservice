<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CustomerImportSeeder extends Seeder
{
    /**
     * Автоматично определя типа на клиента според името
     * Правила:
     * - Ако има "ЕООД", "ООД", "АД", "ET", "EAD" → company
     * - Ако има "ЕТ" (Единноличен търговец) → company  
     * - Ако името е само с големи букви и няма фирмени обозначения → company
     * - Ако има собствено име (Иван Иванов) → individual
     */
    private function determineCustomerType(string $name): string
    {
        $name = trim($name);
        if (empty($name)) return 'company';
        
        // СПИСЪК С ФИРМЕНИ ОБОЗНАЧЕНИЯ
        $companyKeywords = [
            // Български фирмени форми
            'ЕООД', 'ООД', 'АД', 'ЕТ', 'ЕАД', 'СД', 'КДА', 'КД',
            'EОOD', 'OOD', 'AD', 'ET', 'EAD', 'SD', 'KDA', 'KD',
            
            // Международни фирмени форми (в кирилица)
            'ЛТД', 'ИНК', 'КОРП', 'ГМБХ', 'АГ',
            'LTD', 'INC', 'CORP', 'GMBH', 'AG',
            
            // Други обозначения за фирми
            'КОМПАНИЯ', 'КОМПАНИ', 'ФИРМА', 'ПРЕДПРИЯТИЕ',
            'АСОЦИАЦИЯ', 'СДРУЖЕНИЕ', 'ФОНДАЦИЯ', 'ЦЕНТЪР',
            'ИНДУСТРИ', 'ИНДУСТРИЯ', 'ТЪРГОВИЯ', 'ТЪРГОВСКО',
            'ПРОИЗВОДСТВО', 'СЕРВИЗ', 'АВТОСЕРВИЗ', 'СТРОИТЕЛ',
            'ИНЖЕНЕРИНГ', 'КОНСУЛТАНТ', 'КОНСУЛТИНГ',
            'И КО', '& КО', 'И С-И', 'И СИНОВЕ',
        ];
        
        // Проверка за фирмени обозначения в името
        $upperName = mb_strtoupper($name, 'UTF-8');
        
        foreach ($companyKeywords as $keyword) {
            if (str_contains($upperName, $keyword)) {
                return 'company';
            }
        }
        
        // Проверка за ИН (идентификационен номер) в името
        if (preg_match('/\bИН\s*\d{9,13}\b/ui', $name) ||
            preg_match('/\bEIK\s*\d{9,13}\b/ui', $name)) {
            return 'company';
        }
        
        // Правила за определяне на индивидуален клиент
        // Ако името изглежда като пълно име на човек (2-3 думи, първата дума започва с главна буква)
        $words = preg_split('/\s+/', $name);
        $wordCount = count($words);
        
        if ($wordCount >= 2 && $wordCount <= 4) {
            // Проверка дали първата дума изглежда като собствено име
            $firstName = $words[0];
            
            // Често срещани български имена
            $commonFirstNames = [
                'Иван', 'Георги', 'Димитър', 'Петър', 'Николай', 'Стоян',
                'Васил', 'Кръстьо', 'Атанас', 'Стефан', 'Боян', 'Калин',
                'Мария', 'Ивана', 'Елена', 'Гергана', 'Диана', 'Силвия',
                'Петя', 'Весела', 'Радка', 'Цветана', 'Лилия', 'Румяна',
                'Александър', 'Владимир', 'Цветан', 'Красимир', 'Пламен',
                'Теодора', 'Йорданка', 'Милена', 'Надежда', 'Снежана',
            ];
            
            // Проверка дали първата дума е обикновено име
            foreach ($commonFirstNames as $commonName) {
                if (mb_strtoupper($firstName, 'UTF-8') === mb_strtoupper($commonName, 'UTF-8')) {
                    return 'individual';
                }
            }
            
            // Ако първата дума завършва на "ов", "ев", "ин", "ска", "ова" → вероятно е фамилия
            if (preg_match('/(ов|ев|ин|ска|ова)$/ui', $firstName)) {
                // Но проверка дали не е фирма като "Петков и Ко"
                if (!str_contains($upperName, ' И КО') && 
                    !str_contains($upperName, ' & КО') &&
                    !str_contains($upperName, ' И СИНОВЕ')) {
                    return 'individual';
                }
            }
        }
        
        // Ако името е само с големи букви и не е очевидно лице → company
        if ($name === mb_strtoupper($name, 'UTF-8') && 
            !preg_match('/\b(г-н|г-жа|господин|госпожа|д-р|инж\.|арх\.)\b/ui', $name)) {
            return 'company';
        }
        
        // Ако има титла (г-н, г-жа, д-р) → individual
        if (preg_match('/\b(г-н|г-жа|господин|госпожа|д-р|инж\.|арх\.)\b/ui', $name)) {
            return 'individual';
        }
        
        // По подразбиране връщаме company (по-често срещано в бизнес системи)
        return 'company';
    }
    
    /**
     * Тестване на определянето на типа
     */
    private function testTypeDetermination(): void
    {
        $this->command->info('🧪 ТЕСТ НА ОПРЕДЕЛЯНЕТО НА ТИПА:');
        
        $testCases = [
            // ФИРМИ
            'ШАТРОМ ЕООД' => 'company',
            'ТЕРЗИД ЕООД' => 'company',
            'Е.Т.Е. ЕООД' => 'company',
            'ЛИНДНЕР БЪЛГАРИЯ ЕООД' => 'company',
            'ЖАР ЕООД' => 'company',
            'КВЯТ ООД' => 'company',
            'АВТОСТЪКЛА ООД' => 'company',
            'ИВАН ИВАНОВ ЕТ' => 'company',
            'ГЕОРГИ ГЕОРГИЕВ АД' => 'company',
            'ТЕХНОИНДУСТРИЯ ЛТД' => 'company',
            'СОФТУЕРНА КОМПАНИЯ ИНК' => 'company',
            'ПЕТКОВ И КО' => 'company',
            'СЕРВИЗ ЦВЕТАН' => 'company',
            
            // ЧАСТНИ ЛИЦА
            'Иван Иванов' => 'individual',
            'Георги Петров' => 'individual',
            'Мария Стоянова' => 'individual',
            'г-н Димитър Димитров' => 'individual',
            'г-жа Елена Георгиева' => 'individual',
            'д-р Стоян Стоянов' => 'individual',
            'инж. Петър Петров' => 'individual',
            'Цветан Сервиз' => 'individual',
            
            // СПОРНИ СЛУЧАИ
            'ИВАНОВ' => 'company', // Само с големи букви
            'ПЕТКОВ' => 'company', // Само с големи букви
        ];
        
        $passed = 0;
        $total = count($testCases);
        
        $this->command->line("📋 Тестови случаи ($total общо):");
        
        foreach ($testCases as $input => $expected) {
            $result = $this->determineCustomerType($input);
            $isMatch = ($result === $expected);
            
            if ($isMatch) {
                $passed++;
                $this->command->line("✅ " . $this->truncate($input, 25) . 
                                   " → " . $result);
            } else {
                $this->command->line("❌ " . $this->truncate($input, 25) . 
                                   " → " . $result . " (очаквано: $expected)");
            }
        }
        
        $this->command->line(str_repeat('─', 70));
        $percentage = round(($passed / $total) * 100, 1);
        $this->command->info("📊 Резултат: $passed от $total теста минаха успешно ($percentage%)");
        
        if ($passed < $total * 0.8) {
            $this->command->warn("⚠️  Има значителни разминавания в определянето на типа!");
            $this->command->info("💡 Можеш да коригираш правилата в метода determineCustomerType()");
        }
    }
    
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
                if (count($columns) === count($headers)) {
                    $rowData = array_combine($headers, $columns);
                    
                    // ⭐⭐⭐ ВАЖНО: АВТОМАТИЧНО ОПРЕДЕЛЯНЕ НА ТИПА ⭐⭐⭐
                    $customerName = $rowData['Customer-Name'] ?? '';
                    $rowData['_auto_type'] = $this->determineCustomerType($customerName);
                    
                    // Добави към данните само ако има Number
                    if (!empty($rowData['Number'])) {
                        $data[] = $rowData;
                    }
                }
            }
        }
        
        return $data;
    }

    public function run(): void
    {
        $this->command->info('🚀 ИМПОРТ ОТ ACCESS С АВТОМАТИЧНО ОПРЕДЕЛЯНЕ НА ТИПА');
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
        
        // ⭐⭐⭐ ТЕСТ НА ОПРЕДЕЛЯНЕТО НА ТИПА ⭐⭐⭐
        $this->testTypeDetermination();
        
        // ПАРСВАНЕ НА ТАБЛИЧНИЯ ФОРМАТ
        $this->command->info("\n📋 ПАРСВАНЕ НА ТАБЛИЧЕН ФОРМАТ...");
        $tableData = $this->parseTableFormat($content);
        
        if (empty($tableData)) {
            $this->command->error('❌ Не мога да извлека данни от табличния формат!');
            $this->command->info('💡 Експортирай от Access като "Text File" с разделител Tab, не като "Formatted Text"');
            return;
        }
        
        $this->command->info("✅ Намерени записи: " . count($tableData));
        
        // ⭐⭐⭐ СТАТИСТИКА ЗА ТИПОВЕТЕ ПРЕДИ ИМПОРТ ⭐⭐⭐
        $typeStats = ['company' => 0, 'individual' => 0];
        foreach ($tableData as $row) {
            $type = $row['_auto_type'] ?? 'company';
            $typeStats[$type]++;
        }
        
        $this->command->info("📊 Очаквано разпределение на типовете:");
        $this->command->info("   • Фирми (company): {$typeStats['company']}");
        $this->command->info("   • Частни лица (individual): {$typeStats['individual']}");
        
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
        
        // ⭐⭐⭐ ФИНАЛНА СТАТИСТИКА ОТ БАЗАТА ⭐⭐⭐
        $finalStats = Customer::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
        
        $this->command->info("\n📊 ФИНАЛНА СТАТИСТИКА ОТ БАЗАТА ДАННИ:");
        $this->command->info("   • Фирми (company): " . ($finalStats['company'] ?? 0));
        $this->command->info("   • Частни лица (individual): " . ($finalStats['individual'] ?? 0));
        
        if ($imported > 0) {
            $this->command->info("\n🎉 АВТОМАТИЧНОТО ОПРЕДЕЛЯНЕ НА ТИПОВЕТЕ Е ЗАВЪРШЕНО!");
            $this->command->info("💡 Сега можеш да продължиш с импорта на продуктите.");
        }
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
            // ⭐⭐⭐ ИЗПОЛЗВА АВТОМАТИЧНО ОПРЕДЕЛЕНИЯ ТИП ⭐⭐⭐
            'type'                => $converted['_auto_type'] ?? 'company',
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