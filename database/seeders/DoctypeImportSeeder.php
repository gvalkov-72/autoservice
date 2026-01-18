<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctype;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DoctypeImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Импорт на DOCTYPES от Access TXT');

        $filePath = base_path('old-database/doctypes.txt');

        if (!file_exists($filePath)) {
            $this->command->error('❌ Липсва файл: doctypes.txt');
            return;
        }

        $lines = explode("\n", file_get_contents($filePath));

        $header = null;
        $columnCount = 0;

        // 1️⃣ намираме header реда
        foreach ($lines as $line) {
            $line = trim($line);

            if (
                str_starts_with($line, '|') &&
                substr_count($line, '|') >= 3 &&
                !preg_match('/^[\|\-\=\s]+$/', $line)
            ) {
                $headers = $this->parseRow($line);
                $columnCount = count($headers);
                if ($columnCount >= 2) {
                    $header = $headers;
                    break;
                }
            }
        }

        if (!$header) {
            $this->command->error('❌ Header ред не е разпознат');
            return;
        }

        Doctype::truncate();
        $imported = 0;

        // 2️⃣ обхождаме ВСИЧКИ редове и търсим DATA
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

            // първата колона = ID
            if (!is_numeric($cols[0])) {
                continue;
            }

            try {
                Doctype::create([
                    'id'        => (int)$cols[0],
                    'name'      => $this->clean($cols[1] ?? ''),
                    'short'     => $this->clean($cols[2] ?? ''),
                    'ddstype'   => (int)($cols[3] ?? 0),
                    'ajurtype'  => (int)($cols[4] ?? 0),
                    'active'    => true,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now(),
                ]);

                $imported++;
            } catch (\Throwable $e) {
                Log::error('Doctype import error', [
                    'line' => $line,
                    'err'  => $e->getMessage()
                ]);
            }
        }

        $this->command->info("✅ Импортирани doctypes: {$imported}");
    }

    /* ================= helpers ================= */

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
        if (!mb_check_encoding($v, 'UTF-8')) {
            $v = mb_convert_encoding($v, 'UTF-8', 'auto');
        }
        return preg_replace('/\s+/', ' ', str_replace(['??', '?'], '', $v));
    }
}
