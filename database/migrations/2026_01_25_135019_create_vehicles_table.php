<?php
// database/migrations/[timestamp]_create_vehicles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('vehicle')->nullable()->comment('Марка и модел');
            $table->string('plate_number')->nullable()->index();
            $table->string('chassis_number')->nullable()->comment('VIN/Номер на рама');
            $table->integer('last_mileage')->nullable()->comment('Последен известен пробег');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['customer_id', 'plate_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};