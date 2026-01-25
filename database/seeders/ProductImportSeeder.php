<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 IMPORT: PRODUCTS (Access → Laravel)');
        $this->command->info('========================================');

        $filePath = base_path('old-database/products.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ Липсва файл: old-database/products.txt');
            return;
        }

        $lines = array_values(array_filter(
            explode("\n", file_get_contents($filePath)),
            fn($l) => trim($l) !== ''
        ));

        $headerIndex = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/\|\s*PLU\s*\|/i', $line)) {
                $headerIndex = $i;
                break;
            }
        }

        if ($headerIndex === null) {
            $this->command->error('❌ Не е намерен header ред (PLU)');
            return;
        }

        $headers = $this->headers($lines[$headerIndex]);
        $rows = array_slice($lines, $headerIndex + 1);

        $total = $imported = $skipped = $errors = 0;

        foreach ($rows as $line) {
            if ($this->separator($line)) continue;
            $total++;

            try {
                $cols = $this->row($line);
                while (count($cols) < count($headers)) $cols[] = '';

                $data = [];
                foreach ($headers as $i => $h) {
                    $data[$h] = $cols[$i] ?? '';
                }

                $oldId = $this->clean($data['PLU'] ?? null);
                if (!$oldId) {
                    $skipped++;
                    continue;
                }

                if (Product::where('old_id', $oldId)->exists()) {
                    $skipped++;
                    continue;
                }

                Product::create([
                    'old_id' => $oldId,
                    'name' => $this->clean($data['Name'] ?? 'Без име'),
                    'uom' => $this->clean($data['UOM'] ?? null),
                    'quantity' => $this->num($data['Qty'] ?? 0),
                    'price' => $this->num($data['Price'] ?? 0),
                    'account' => $this->clean($data['acc'] ?? null),
                    'is_active' => true,
                ]);

                $imported++;

                if ($imported <= 3) {
                    $this->command->info("✔ {$oldId} → {$data['Name']}");
                }

            } catch (\Throwable $e) {
                $errors++;
                Log::error('Product import error', [
                    'line' => $line,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->command->info('========================================');
        $this->command->info("📦 Общо редове: {$total}");
        $this->command->info("✅ Импортирани: {$imported}");
        $this->command->info("⏭ Пропуснати: {$skipped}");
        $this->command->info("❌ Грешки: {$errors}");
        $this->command->info('========================================');
    }

    /* ================= HELPERS ================= */

    private function headers(string $line): array
    {
        return array_values(array_filter(
            array_map('trim', explode('|', trim($line, '| ')))
        ));
    }

    private function row(string $line): array
    {
        return array_map('trim', explode('|', trim($line, '| ')));
    }

    private function separator(string $line): bool
    {
        return preg_match('/^[\|\-\=\s]+$/', trim($line));
    }

    private function clean(?string $v): ?string
    {
        if (!$v) return null;
        if (!mb_check_encoding($v, 'UTF-8')) {
            $v = mb_convert_encoding($v, 'UTF-8', 'Windows-1251');
        }
        return trim($v) === '' ? null : trim($v);
    }

    private function num($v): float
    {
        $v = str_replace(',', '.', (string)$v);
        return is_numeric($v) ? (float)$v : 0;
    }
}
