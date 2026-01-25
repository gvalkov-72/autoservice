<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Връзка със стария Access
            $table->string('old_id')->nullable()->index(); // PLU

            // Основни данни
            $table->string('name');
            $table->string('uom')->nullable(); // Unit of Measure
            $table->decimal('quantity', 12, 3)->default(0);
            $table->decimal('price', 12, 2)->default(0);

            // Счетоводство
            $table->string('account')->nullable(); // acc

            // Флагове
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
