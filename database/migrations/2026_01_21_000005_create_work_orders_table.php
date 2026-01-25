<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {

            // Laravel PK
            $table->id();

            // Access: PO (ID)
            $table->unsignedInteger('old_id')->unique();

            // Access: Клиент
            $table->string('client_name')->nullable(); // Access не винаги има customer_id

            // Дата на поръчката
            $table->date('order_date')->nullable();

            // Автор / Приел поръчката
            $table->string('created_by')->nullable();

            // Забележка
            $table->text('note')->nullable();

            // Автомобилна информация
            $table->string('vehicle')->nullable();        // Автомобил
            $table->string('chassis_number')->nullable(); // Шаси
            $table->string('plate_number')->nullable();   // ДК №

            // Контакт
            $table->string('phone')->nullable();

            // Сервизна информация
            $table->unsignedInteger('mechanic_code')->nullable(); // Код на монтьора
            $table->unsignedInteger('mileage')->nullable();       // Изминати км

            // Сума за услуга (само труд)
            $table->decimal('service_amount', 12, 2)->default(0);

            // Laravel timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
