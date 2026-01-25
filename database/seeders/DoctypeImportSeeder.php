<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctypeImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🚀 IMPORT: DOCTYPES (Access → Laravel)');
        $this->command->info('========================================');

        $file = base_path('old-database/doctypes.txt');

        if (!file_exists($file)) {
            $this->command->error('❌ Липсва doctypes.txt');
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);

        $imported = 0;
        $skipped  = 0;

        foreach ($lines as $line) {

            $line = trim($line);

            // празни / разделителни редове
            if ($line === '' || preg_match('/^[\|\-\s]+$/', $line)) {
                continue;
            }

            // махаме водещи и крайни |
            $line = trim($line, '|');

            // split по |
            $cols = array_map('trim', explode('|', $line));

            // header ред
            if (isset($cols[0]) && strtolower($cols[0]) === 'type') {
                continue;
            }

            if (count($cols) < 5) {
                $skipped++;
                continue;
            }

            [$type, $name, $short, $ddstype, $ajurtype] = $cols;

            if (!is_numeric($type)) {
                $skipped++;
                continue;
            }

            // encoding fix
            if (!mb_check_encoding($name, 'UTF-8')) {
                $name = mb_convert_encoding($name, 'UTF-8', 'Windows-1251');
            }

            if (!mb_check_encoding($short, 'UTF-8')) {
                $short = mb_convert_encoding($short, 'UTF-8', 'Windows-1251');
            }

            // защита от дублиране
            if (DB::table('doctypes')->where('type', (int)$type)->exists()) {
                $this->command->warn("⏭ Пропускане: type {$type} вече съществува");
                $skipped++;
                continue;
            }

            DB::table('doctypes')->insert([
                'type'       => (int)$type,
                'name'       => trim($name),
                'short'      => trim($short),
                'ddstype'    => trim($ddstype),
                'ajurtype'   => (int)$ajurtype,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $imported++;

            $this->command->info("✔ {$type} → {$name}");
        }

        $this->command->info('========================================');
        $this->command->info("✅ IMPORT FINISHED");
        $this->command->info("📄 Импортирани: {$imported}");
        $this->command->info("⏭ Пропуснати: {$skipped}");
        $this->command->info('========================================');
    }
}
