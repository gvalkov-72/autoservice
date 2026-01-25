<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 IMPORT: CUSTOMERS (Access → Laravel)');
        $this->command->info('========================================');

        $filePath = base_path('old-database/customer.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ Липсва файл: old-database/customer.txt');
            return;
        }

        $content = file_get_contents($filePath);
        if (!$content) {
            $this->command->error('❌ Файлът е празен или не може да се прочете');
            return;
        }

        $lines = array_values(array_filter(
            explode("\n", $content),
            fn($l) => trim($l) !== ''
        ));

        if (count($lines) < 2) {
            $this->command->error('❌ Няма достатъчно данни във файла');
            return;
        }

        // Намиране на header реда
        $headerIndex = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/\|\s*Number\s*\|/i', $line)) {
                $headerIndex = $i;
                break;
            }
        }

        if ($headerIndex === null) {
            $this->command->error('❌ Не е намерен header ред (Number)');
            return;
        }

        $headers = $this->extractHeaders($lines[$headerIndex]);
        $dataLines = array_slice($lines, $headerIndex + 1);

        $total = $imported = $skipped = $errors = 0;
        $start = microtime(true);

        foreach ($dataLines as $line) {
            if ($this->isSeparator($line)) {
                continue;
            }

            $total++;

            try {
                $cols = $this->parseRow($line);

                if (count($cols) < count($headers)) {
                    while (count($cols) < count($headers)) {
                        $cols[] = '';
                    }
                }

                $row = [];
                foreach ($headers as $i => $h) {
                    $row[$h] = $cols[$i] ?? '';
                }

                $oldId = $this->clean($row['Number'] ?? null);
                if (!$oldId) {
                    $skipped++;
                    continue;
                }

                if (Customer::where('old_id', $oldId)->exists()) {
                    $skipped++;
                    continue;
                }

                $customer = Customer::create([
                    'old_id' => $oldId,
                    'customer_number' => $oldId,
                    'name' => $this->clean($row['Customer-Name'] ?? 'Без име'),
                    'email' => $this->email($row['E-mail'] ?? null),
                    'phone' => $this->phone($row['Telno'] ?? null),
                    'fax' => $this->phone($row['Faxno'] ?? null),

                    'address' => $this->clean($row['Customer-Address-1'] ?? null),
                    'address_2' => $this->clean($row['Customer-Address-2'] ?? null),
                    'res_address_1' => $this->clean($row['ResAddress1'] ?? null),
                    'res_address_2' => $this->clean($row['ResAddress2'] ?? null),

                    'mol' => $this->clean($row['Customer-MOL'] ?? null),
                    'contact_person' => $this->clean($row['Contact'] ?? null),
                    'tax_number' => $this->clean($row['Customer-Taxno'] ?? null),
                    'bulstat' => $this->clean($row['Customer-Bulstat'] ?? null),
                    'bulstat_letter' => $this->clean($row['bulstatletter'] ?? null),
                    'doc_type' => $this->clean($row['Customer-DocType'] ?? null),

                    'receiver' => $this->clean($row['Receiver'] ?? null),
                    'receiver_details' => $this->clean($row['Receiver Details'] ?? null),

                    'eidate' => $this->date($row['eidate'] ?? null),
                    'partida' => $this->clean($row['partida'] ?? null),
                    'notes' => null,

                    'include_in_mailing' => $this->bool($row['include'] ?? 1),
                    'is_active' => $this->bool($row['active'] ?? 1),
                    'is_customer' => $this->bool($row['customer'] ?? 1),
                    'is_supplier' => $this->bool($row['supplier'] ?? 0),

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imported++;

                if ($imported <= 3) {
                    $this->command->info("✔ {$customer->old_id} → {$customer->name}");
                }

            } catch (\Throwable $e) {
                $errors++;
                Log::error('Customer import error', [
                    'line' => $line,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $time = round(microtime(true) - $start, 2);

        $this->command->info('========================================');
        $this->command->info("📦 Общо редове: {$total}");
        $this->command->info("✅ Импортирани: {$imported}");
        $this->command->info("⏭ Пропуснати: {$skipped}");
        $this->command->info("❌ Грешки: {$errors}");
        $this->command->info("⏱ Време: {$time}s");
        $this->command->info('========================================');
    }

    /* ================= HELPERS ================= */

    private function extractHeaders(string $line): array
    {
        return array_values(array_filter(
            array_map('trim', explode('|', trim($line, '| ')))
        ));
    }

    private function parseRow(string $line): array
    {
        return array_map('trim', explode('|', trim($line, '| ')));
    }

    private function isSeparator(string $line): bool
    {
        return preg_match('/^[\|\-\=\s]+$/', trim($line));
    }

    private function clean(?string $v): ?string
    {
        if (!$v) return null;
        $v = trim($v);
        if (!mb_check_encoding($v, 'UTF-8')) {
            $v = mb_convert_encoding($v, 'UTF-8', 'Windows-1251');
        }
        return $v === '' ? null : $v;
    }

    private function phone(?string $v): ?string
    {
        if (!$v) return null;
        return preg_replace('/[^0-9+\s]/', '', $v);
    }

    private function email(?string $v): ?string
    {
        $v = $this->clean($v);
        return $v && filter_var($v, FILTER_VALIDATE_EMAIL) ? strtolower($v) : null;
    }

    private function bool($v): bool
    {
        return in_array(mb_strtolower((string)$v), ['1','yes','true','да','on','active']);
    }

    private function date(?string $v): ?string
    {
        if (!$v) return null;
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
