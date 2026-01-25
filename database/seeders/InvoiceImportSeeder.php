<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class InvoiceImportSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('old-database/invoices.txt');

        if (!File::exists($file)) {
            $this->command->error('❌ Файлът invoices.txt не е намерен');
            return;
        }

        $lines = File::lines($file)->toArray();

        $this->command->info(str_repeat('=', 40));
        $this->command->info('🚀 IMPORT: INVOICES (Access → Laravel)');
        $this->command->info(str_repeat('=', 40));

        $bar = $this->command->getOutput()->createProgressBar(count($lines));
        $bar->start();

        $inserted = 0;

        foreach ($lines as $line) {

            $line = trim($line);

            // Пропускаме празни и разделителни редове
            if ($line === '' || str_starts_with($line, '---')) {
                $bar->advance();
                continue;
            }

            // Премахваме водещи и крайни |
            $line = trim($line, '|');

            $cols = array_map('trim', explode('|', $line));

            /**
             * Очакваме минимум 19 колони
             */
            if (count($cols) < 19) {
                $bar->advance();
                continue;
            }

            /**
             * Header защита
             */
            if (!is_numeric($cols[0])) {
                $bar->advance();
                continue;
            }

            [
                $oldId,
                $customerOldId,
                $invoiceDate,
                $invoiceType,
                $invoiceReceivedDate,
                $invoiceReceivedPerson,
                $invoiceCreatedBy,
                $note,
                $paymentCash,
                $void,
                $printed,
                $tipsdelka,
                $saleType,
                $payMethod,
                $paid,
                $invoiceRecResponsible,
                $invoiceCreResponsible,
                $dateDue,
                $zeroexplain
            ] = $cols;

            $oldId = (int) $oldId;

            if ($oldId <= 0) {
                $bar->advance();
                continue;
            }

            DB::table('invoices')->updateOrInsert(
                ['old_id' => $oldId],
                [
                    'customer_old_id' => (int) $customerOldId,
                    'invoice_type' => (int) $invoiceType,

                    'invoice_date' => $this->parseDate($invoiceDate),
                    'invoice_received_date' => $this->parseDate($invoiceReceivedDate),
                    'date_due' => $this->parseDate($dateDue),

                    'invoice_received_person' => $this->clean($invoiceReceivedPerson),
                    'invoice_created_by' => $this->clean($invoiceCreatedBy),
                    'invoice_rec_responsible' => $this->clean($invoiceRecResponsible),
                    'invoice_cre_responsible' => $this->clean($invoiceCreResponsible),

                    'note' => $this->clean($note),
                    'zeroexplain' => $this->clean($zeroexplain),

                    'payment_cash' => $this->yesNo($paymentCash),
                    'is_void' => $this->yesNo($void),
                    'printed' => $this->yesNo($printed),
                    'paid' => $this->yesNo($paid),

                    'tipsdelka' => (int) $tipsdelka,
                    'sale_type' => (int) $saleType,
                    'pay_method' => (int) $payMethod,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $inserted++;
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        $this->command->info("✅ IMPORT FINISHED | Общо: {$inserted}");
        $this->command->info(str_repeat('=', 40));
    }

    /**
     * Парсване на дата от Access формат: 17.1.2007 г.
     */
    private function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '??') {
            return null;
        }

        $value = str_replace([' г.', 'г.'], '', $value);

        try {
            return Carbon::createFromFormat('j.n.Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Да / Не → boolean
     */
    private function yesNo(?string $value): bool
    {
        return mb_strtolower(trim((string) $value)) === 'да';
    }

    /**
     * Общ текстов чистач
     */
    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' || $value === '??' ? null : $value;
    }
}
