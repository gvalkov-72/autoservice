<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавя връзка между фактури и автомобили
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            // vehicle_id е nullable, защото имаме стари данни
            $table->foreignId('vehicle_id')
                  ->nullable()
                  ->after('customer_id')
                  ->constrained('vehicles')
                  ->nullOnDelete();
        });
    }

    /**
     * Връщане назад
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
    }
};
