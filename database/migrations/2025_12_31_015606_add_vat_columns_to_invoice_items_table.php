<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {

            // Процент ДДС (20, 9, 0 или произволен)
            $table->decimal('vat_rate', 5, 2)
                ->default(20.00)
                ->after('unit_price')
                ->comment('VAT rate in percent');

            // Стойност на ДДС за реда
            $table->decimal('vat_amount', 12, 2)
                ->default(0)
                ->after('vat_rate')
                ->comment('VAT amount for this invoice item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['vat_rate', 'vat_amount']);
        });
    }
};
