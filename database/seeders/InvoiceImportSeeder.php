<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InvoiceImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 СТАРТИРАНЕ НА ИМПОРТ НА ФАКТУРИ ОТ TXT ФАЙЛ');
        $this->command->info('========================================');

        $filePath = base_path('old-database/invoices.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ ФАЙЛЪТ НЕ Е НАМЕРЕН: invoices.txt');
            return;
        }

        $this->command->info('📁 Прочитане на файл: ' . $filePath);
        
        // Изтриване на съществуващи данни
        $this->command->info('🗑️  Изтриване на стари записи...');
        Schema::disableForeignKeyConstraints();
        DB::table('invoices')->truncate();
        Schema::enableForeignKeyConstraints();

        // Четем целия файл
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        $totalLines = count($lines);
        $this->command->info("📊 Общо редове във файла: {$totalLines}");
        
        $importedCount = 0;
        $skippedCount = 0;
        $dataLineNumber = 0;
        
        $this->command->info('🔍 Търсене на редове с данни...');
        
        // Търсим мястото, където започват данните
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
            $this->command->info('💡 Първите 10 реда от файла:');
            for ($i = 0; $i < min(10, $totalLines); $i++) {
                $this->command->info('   ' . ($i + 1) . ': ' . substr(trim($lines[$i]), 0, 100));
            }
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
            
            // Проверка дали е ред с данни (започва с | и число)
            if (!preg_match('/^\|.*\|$/', $line) || !preg_match('/\d+/', $line)) {
                $progressBar->advance();
                continue;
            }

            $dataLineNumber++;
            
            try {
                // Извличане на данните
                $invoiceData = $this->parseInvoiceLine($line);
                
                if ($invoiceData === null) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }
                
                // Създаване на записа
                Invoice::create($invoiceData);
                $importedCount++;
                
                // Показване на прогреса
                if ($importedCount % 100 === 0) {
                    $this->command->info("\n   ... импортирани {$importedCount} фактури ...");
                }
                
            } catch (\Exception $e) {
                Log::warning("Грешка при ред {$i}: {$e->getMessage()}");
                $skippedCount++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine(2);
        
        // Резюме
        $this->command->info('✅ ИМПОРТЪТ Е ЗАВЪРШЕН');
        $this->command->info('========================================');
        $this->command->info("📈 Импортирани записи: {$importedCount}");
        $this->command->info("⏭️  Пропуснати записи: {$skippedCount}");
        $this->command->info("📋 Обработени редове с данни: {$dataLineNumber}");
        $this->command->info('========================================');
        
        if ($importedCount > 0) {
            $this->command->info('🎉 Успешно импортирахте ' . $importedCount . ' фактури!');
            $this->showImportStatistics();
        }
    }
    
    /**
     * Парсва ред от файла
     */
    private function parseInvoiceLine(string $line): ?array
    {
        // Премахваме началния и крайния |
        $line = trim($line, '|');
        
        // Разделяме по |
        $columns = explode('|', $line);
        
        // Тримваме всяка колона
        $columns = array_map('trim', $columns);
        
        // Брой на колоните (според хедъра трябва да са 19)
        $columnCount = count($columns);
        
        if ($columnCount < 3) {
            return null;
        }
        
        // Създаваме масив с данни
        $data = [
            'invoice_number' => null,
            'customer_id' => null,
            'invoice_date' => null,
            'status' => 'sent',
            'payment_status' => 'pending',
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_tax_amount' => 0,
            'discount_amount' => 0,
            'grand_total' => 0,
            'payment_cash' => 0,
            'is_void' => false,
            'is_printed' => false,
            'is_paid' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // Колона 0: Invoice-I (ID)
        if (isset($columns[0])) {
            $id = $this->cleanNumber($columns[0]);
            if ($id) {
                $data['id'] = $id;
                $data['invoice_number'] = $id;
            }
        }
        
        // Колона 1: Customer-ID
        if (isset($columns[1])) {
            $customerId = $this->cleanNumber($columns[1]);
            if ($customerId) {
                $data['customer_id'] = $customerId;
            }
        }
        
        // Колона 2: Invoice-Date
        if (isset($columns[2])) {
            $date = $this->parseDate($columns[2]);
            if ($date) {
                $data['invoice_date'] = $date;
                $data['due_date'] = $date;
                $data['date_due'] = $date;
            }
        }
        
        // Колона 3: Invoice-Ty (Type)
        if (isset($columns[3])) {
            $data['invoice_type'] = $this->parseInvoiceType($columns[3]);
        }
        
        // Колона 4: Invoice-Received-Date
        if (isset($columns[4])) {
            $receivedDate = $this->parseDate($columns[4]);
            if ($receivedDate) {
                $data['received_date'] = $receivedDate;
            }
        }
        
        // Колона 5: Invoice-Received-Person
        if (isset($columns[5]) && trim($columns[5]) !== '') {
            $data['received_person'] = trim($columns[5]);
        }
        
        // Колона 6: Invoice-Съставил
        if (isset($columns[6]) && trim($columns[6]) !== '') {
            $data['invoice_cre_responsible'] = trim($columns[6]);
        }
        
        // Колона 7: Note
        if (isset($columns[7]) && trim($columns[7]) !== '') {
            $data['notes'] = trim($columns[7]);
        }
        
        // Колона 8: paymentcash
        if (isset($columns[8])) {
            $data['payment_cash'] = $this->parseMoney($columns[8]);
        }
        
        // Колона 9: void
        if (isset($columns[9])) {
            $data['is_void'] = $this->parseBoolean($columns[9]);
            if ($data['is_void']) {
                $data['status'] = 'voided';
            }
        }
        
        // Колона 10: Printed
        if (isset($columns[10])) {
            $data['is_printed'] = $this->parseBoolean($columns[10]);
        }
        
        // Колона 11: tipsdelka
        if (isset($columns[11])) {
            $data['tips_deka'] = trim($columns[11]);
        }
        
        // Колона 12: saletype
        if (isset($columns[12])) {
            $data['sale_type'] = $this->parseSaleType($columns[12]);
        }
        
        // Колона 13: paymethod
        if (isset($columns[13])) {
            $data['payment_iod'] = trim($columns[13]);
            $data['payment_method'] = $this->parsePaymentMethod($columns[13]);
        }
        
        // Колона 14: Paid
        if (isset($columns[14])) {
            $data['is_paid'] = $this->parseBoolean($columns[14]);
            $data['payment_status'] = $data['is_paid'] ? 'paid' : 'pending';
        }
        
        // Колона 15: InvoiceRecRespons
        if (isset($columns[15]) && trim($columns[15]) !== '') {
            $data['invoice_rec_responsible'] = trim($columns[15]);
        }
        
        // Колона 16: InvoiceCreRespons
        if (isset($columns[16]) && trim($columns[16]) !== '') {
            if (empty($data['invoice_cre_responsible'])) {
                $data['invoice_cre_responsible'] = trim($columns[16]);
            }
        }
        
        // Колона 17: datedue
        if (isset($columns[17])) {
            $dueDate = $this->parseDate($columns[17]);
            if ($dueDate) {
                $data['due_date'] = $dueDate;
                $data['date_due'] = $dueDate;
            }
        }
        
        // Колона 18: zeroexplain
        if (isset($columns[18]) && trim($columns[18]) !== '') {
            $data['zero_explain'] = trim($columns[18]);
        }
        
        // Колона 6 се използва и за additional_info
        if (isset($columns[6]) && trim($columns[6]) !== '') {
            $data['additional_info'] = trim($columns[6]);
        }
        
        // Проверка за валидност
        if (empty($data['invoice_number']) || empty($data['invoice_date'])) {
            return null;
        }
        
        return $data;
    }
    
    /**
     * Почиства число от празни пространства и специални символи
     */
    private function cleanNumber(string $value): ?int
    {
        $value = trim($value);
        
        if ($value === '' || $value === '??' || $value === 'NULL') {
            return null;
        }
        
        // Премахваме всички не-числови символи (освен минус за отрицателни числа)
        $value = preg_replace('/[^\d-]/', '', $value);
        
        if ($value === '' || $value === '-') {
            return null;
        }
        
        return (int)$value;
    }
    
    /**
     * Парсва българска дата
     */
    private function parseDate(string $value): ?string
    {
        $value = trim($value);
        
        if ($value === '' || $value === '??' || $value === 'NULL') {
            return null;
        }
        
        // Премахваме '?.' ако има
        $value = str_replace('?.', '', $value);
        $value = trim($value);
        
        try {
            // Опитваме формат dd.mm.yyyy
            if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $value, $matches)) {
                $day = (int)$matches[1];
                $month = (int)$matches[2];
                $year = (int)$matches[3];
                
                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
            
            // Опитваме формат dd.mm.yy
            if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{2})$/', $value, $matches)) {
                $day = (int)$matches[1];
                $month = (int)$matches[2];
                $year = (int)$matches[3] + 2000;
                
                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
            
            // Опитваме само година
            if (is_numeric($value) && strlen($value) === 4) {
                return $value . '-01-01';
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Парсва парична стойност
     */
    private function parseMoney(string $value): float
    {
        $value = trim($value);
        
        if ($value === '' || $value === '??' || $value === 'NULL') {
            return 0.0;
        }
        
        // Премахваме валутни символи и празни пространства
        $value = str_replace(['лв', 'лв.', 'BGN', ' ', ','], '', $value);
        
        return is_numeric($value) ? (float)$value : 0.0;
    }
    
    /**
     * Парсва булева стойност
     */
    private function parseBoolean(string $value): bool
    {
        $value = strtolower(trim($value));
        
        if ($value === '' || $value === '??' || $value === 'null') {
            return false;
        }
        
        // Ако е число
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        
        // Ако е текст
        return in_array($value, ['да', 'yes', 'true', 'y', 't']);
    }
    
    /**
     * Парсва тип фактура
     */
    private function parseInvoiceType(string $value): string
    {
        $value = trim($value);
        
        if ($value === '' || $value === '??') {
            return 'standard';
        }
        
        switch ($value) {
            case '0': return 'invoice';
            case '1': return 'proforma';
            case '2': return 'credit_note';
            case '3': return 'debit_note';
            default: return 'standard';
        }
    }
    
    /**
     * Парсва тип продажба
     */
    private function parseSaleType(string $value): ?string
    {
        $value = trim($value);
        
        if ($value === '' || $value === '??') {
            return null;
        }
        
        switch ($value) {
            case '0': return 'retail';
            case '1': return 'wholesale';
            case '2': return 'service';
            default: return $value;
        }
    }
    
    /**
     * Парсва метод на плащане
     */
    private function parsePaymentMethod(string $value): string
    {
        $value = trim($value);
        
        if ($value === '' || $value === '??') {
            return 'unknown';
        }
        
        switch ($value) {
            case '0': return 'cash';
            case '1': return 'bank_transfer';
            case '2': return 'card';
            case '3': return 'credit';
            default: return 'unknown';
        }
    }
    
    /**
     * Показва статистика след импорт
     */
    private function showImportStatistics(): void
    {
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('is_paid', true)->count();
        $voidInvoices = Invoice::where('is_void', true)->count();
        $uniqueCustomers = Invoice::whereNotNull('customer_id')->distinct('customer_id')->count('customer_id');
        
        $this->command->info("📊 СТАТИСТИКА:");
        $this->command->info("   • Общо фактури: {$totalInvoices}");
        $this->command->info("   • Платени: {$paidInvoices}");
        $this->command->info("   • Анулирани: {$voidInvoices}");
        $this->command->info("   • Уникални клиенти: {$uniqueCustomers}");
        
        // Първи и последен запис
        $firstInvoice = Invoice::orderBy('invoice_date')->first();
        $lastInvoice = Invoice::orderBy('invoice_date', 'desc')->first();
        
        if ($firstInvoice && $lastInvoice) {
            $this->command->info("   • Период: {$firstInvoice->invoice_date} до {$lastInvoice->invoice_date}");
        }
        
        // Примерен запис
        $sample = Invoice::first();
        if ($sample) {
            $this->command->info("📋 Примерна фактура #{$sample->invoice_number}:");
            $this->command->info("   • Клиент: " . ($sample->customer_id ?? 'N/A'));
            $this->command->info("   • Дата: {$sample->invoice_date}");
            $this->command->info("   • Статус: {$sample->status}");
            $this->command->info("   • Плащане: {$sample->payment_status}");
        }
    }
    
    /**
     * Проверка на структурата на файла
     */
    public static function checkFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'Файлът не съществува'];
        }
        
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        $dataLines = [];
        
        // Намираме първия ред с данни
        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            
            if (empty($trimmed)) {
                continue;
            }
            
            // Търсим ред, който започва с | и число
            if (preg_match('/^\|\s+\d+\s+\|/', $trimmed)) {
                $dataLines[] = [
                    'line_number' => $i + 1,
                    'content' => substr($trimmed, 0, 150) . '...',
                    'column_count' => count(explode('|', $trimmed)) - 1
                ];
                
                if (count($dataLines) >= 3) {
                    break;
                }
            }
        }
        
        if (empty($dataLines)) {
            return ['success' => false, 'message' => 'Не са намерени данни', 'first_lines' => array_slice($lines, 0, 5)];
        }
        
        return [
            'success' => true,
            'total_lines' => count($lines),
            'data_lines_found' => count($dataLines),
            'samples' => $dataLines,
            'format' => 'access_table_with_borders'
        ];
    }
}