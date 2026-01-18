<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentMethodImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Импорт на PAYMENT METHODS от Access TXT');

        $filePath = base_path('old-database/payment_methods.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ Липсва файл: payment_methods.txt');
            return;
        }

        $lines = explode("\n", file_get_contents($filePath));

        $header = null;
        $columnCount = 0;

        // 1️⃣ намиране на header ред
        foreach ($lines as $line) {
            $line = trim($line);

            if (
                str_starts_with($line, '|') &&
                substr_count($line, '|') >= 3 &&
                !preg_match('/^[\|\-\=\s]+$/', $line)
            ) {
                $header = $this->parseRow($line);
                $columnCount = count($header);
                if ($columnCount >= 2) {
                    break;
                }
            }
        }

        if (!$header) {
            $this->command->error('❌ Header ред не е намерен');
            return;
        }

        PaymentMethod::truncate();
        $imported = 0;

        // 2️⃣ data редове
        foreach ($lines as $line) {
            $line = trim($line);

            if (!str_starts_with($line, '|')) {
                continue;
            }

            if (preg_match('/^[\|\-\=\s]+$/', $line)) {
                continue;
            }

            $cols = $this->parseRow($line);

            if (count($cols) !== $columnCount) {
                continue;
            }

            if (!is_numeric($cols[0])) {
                continue;
            }

            try {
                PaymentMethod::create([
                    'id'        => (int)$cols[0],
                    'name'      => $this->clean($cols[1] ?? ''),
                    'short'     => $this->clean($cols[2] ?? ''),
                    'is_cash'   => $this->parseBool($cols[3] ?? 0),
                    'is_bank'   => $this->parseBool($cols[4] ?? 0),
                    'active'    => true,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now(),
                ]);

                $imported++;
            } catch (\Throwable $e) {
                Log::error('PaymentMethod import error', [
                    'line' => $line,
                    'err'  => $e->getMessage()
                ]);
            }
        }

        $this->command->info("✅ Импортирани payment methods: {$imported}");
    }

    /* ===== helpers ===== */

    private function parseRow(string $line): array
    {
        return array_values(array_map('trim', explode('|', trim($line, '| '))));
    }

    private function clean($v): string
    {
        $v = trim((string)$v);

        if ($v === '') {
            return '';
        }

        if (!mb_check_encoding($v, 'UTF-8')) {
            $v = mb_convert_encoding($v, 'UTF-8', 'auto');
        }

        return preg_replace('/\s+/', ' ', str_replace(['??', '?'], '', $v));
    }

    private function parseBool($v): bool
    {
        if (is_numeric($v)) {
            return (bool)$v;
        }

        $v = strtolower(trim((string)$v));
        return in_array($v, ['1', 'yes', 'true', 'да', 'on'], true);
    }
}
