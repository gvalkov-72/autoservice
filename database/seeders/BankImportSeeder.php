<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BankImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Импорт на BANKS от Access TXT');

        $filePath = base_path('old-database/banks.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ Липсва файл: banks.txt');
            return;
        }

        $lines = explode("\n", file_get_contents($filePath));
        $header = null;
        $columnCount = 0;

        // Намерете заглавията на колоните
        foreach ($lines as $line) {
            $line = trim($line);

            if (
                str_starts_with($line, '|') &&
                substr_count($line, '|') >= 3 &&
                !preg_match('/^[\|\-\=\s]+$/', $line)
            ) {
                $headers = $this->parseRow($line);
                $columnCount = count($headers);

                if ($columnCount >= 3) {
                    $header = $headers;
                    break;
                }
            }
        }

        if (!$header) {
            $this->command->error('❌ Header ред не е разпознат');
            return;
        }

        $imported = 0;

        // Обработване на данни
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
                // updateOrCreate вместо truncate + create
                Bank::updateOrCreate(
                    ['id' => (int)$cols[0]],
                    [
                        'account'    => $this->clean($cols[1] ?? ''),
                        'bank_code'  => $this->clean($cols[2] ?? ''),
                        'name'       => $this->clean($cols[3] ?? ''),
                        'currency'   => $this->clean($cols[4] ?? 'BGN'),
                        'method'     => (int)($cols[5] ?? 1),
                        'type'       => (int)($cols[6] ?? 1),
                        'short_name' => $this->clean($cols[7] ?? ''),
                        'is_default' => $this->parseBool($cols[8] ?? 0),
                        'active'     => true,
                        'updated_at' => Carbon::now(),
                        'created_at' => Bank::find($cols[0])?->created_at ?? Carbon::now(),
                    ]
                );

                $imported++;
            } catch (\Throwable $e) {
                Log::error('Bank import error', [
                    'line' => $line,
                    'err'  => $e->getMessage()
                ]);
            }
        }

        $this->command->info("✅ Импортирани банки: {$imported}");
    }

    private function parseRow(string $line): array
    {
        return array_values(
            array_map(
                'trim',
                explode('|', trim($line, '| '))
            )
        );
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
        if (is_bool($v)) {
            return $v;
        }

        if (is_numeric($v)) {
            return (bool)$v;
        }

        $v = strtolower(trim((string)$v));
        return in_array($v, ['1', 'yes', 'true', 'да', 'on'], true);
    }
}
