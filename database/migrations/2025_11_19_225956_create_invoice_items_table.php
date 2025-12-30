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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->integer('line_number')->nullable(); // Access: Number
            $table->string('product_code')->nullable(); // Access: item-Code
            $table->text('description')->nullable(); // Access: Item-Name
            $table->string('unit_of_measure')->nullable(); // Access: Item (вероятно е единица мярка)
            $table->decimal('quantity', 10, 2)->default(0); // Access: Item (втори, вероятно количество)
            $table->decimal('unit_price', 10, 2)->default(0); // Access: Item-Price-Ea
            $table->decimal('total_price', 10, 2)->default(0); // Access: Item-total
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Индекси
            $table->index(['invoice_id', 'line_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};