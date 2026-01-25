<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_items', function (Blueprint $table) {

            // FK към work_orders
            $table->foreignId('work_order_id')
                ->nullable()
                ->after('work_order_old_id')
                ->constrained('work_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_order_items', function (Blueprint $table) {

            $table->dropForeign(['work_order_id']);
            $table->dropColumn('work_order_id');
        });
    }
};
