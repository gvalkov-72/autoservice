<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('change'); // + или -
            $table->enum('type', ['purchase','sale','reservation','return','adjustment']);
            $table->unsignedBigInteger('reference_id')->nullable(); // work_order_id, invoice_id и т.н.
            $table->string('reference_type')->nullable(); // App\Models\WorkOrder ...
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['product_id', 'type']);
            $table->index(['reference_id', 'reference_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};