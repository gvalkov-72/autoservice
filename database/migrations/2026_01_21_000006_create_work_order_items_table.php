<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_items', function (Blueprint $table) {

            // Laravel PK
            $table->id();

            // Access: POID → work_orders.old_id (БЕЗ FK)
            $table->unsignedInteger('work_order_old_id')->index();

            // Access: Number
            $table->unsignedInteger('row_number')->nullable();

            // Access: Item-Code
            $table->string('item_code')->nullable();

            // Access: Item-Name
            $table->string('item_name')->nullable();

            // Access: Item-Measure
            $table->string('item_measure')->nullable();

            // Access: Item-Qty
            $table->decimal('quantity', 10, 2)->default(0);

            // Access: Item-Price-Each
            $table->decimal('price_each', 12, 2)->default(0);

            // Access: Item-total
            $table->decimal('row_total', 12, 2)->default(0);

            // Laravel timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_items');
    }
};
