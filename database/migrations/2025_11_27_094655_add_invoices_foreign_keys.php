<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // добавяме САМО ако НЕ съществува
            if (! $this->fkExists('invoices', 'invoices_created_by_foreign')) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if ($this->fkExists('invoices', 'invoices_created_by_foreign')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
        }
    }

    /* помощна: проверява дали FK съществува */
    private function fkExists(string $table, string $name): bool
    {
        return DB::select("SELECT 1 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?", [$table, $name]) !== [];
    }
};