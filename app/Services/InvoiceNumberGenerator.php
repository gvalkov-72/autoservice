<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    public static function next(): string
    {
        return DB::transaction(function () {

            $counter = DB::table('invoice_counters')
                ->lockForUpdate()
                ->first();

            $nextNumber = $counter->current_number + 1;

            DB::table('invoice_counters')->update([
                'current_number' => $nextNumber,
                'updated_at' => now(),
            ]);

            // 10-цифрен формат с водещи нули
            return str_pad($nextNumber, 10, '0', STR_PAD_LEFT);
        });
    }
}
