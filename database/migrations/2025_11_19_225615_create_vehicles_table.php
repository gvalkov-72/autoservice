<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Стар системен идентификатор и партиден импорт
            $table->string('old_system_id')->nullable()->comment('ID от старата Access система');
            $table->string('import_batch')->nullable()->comment('Бач/група за импортиране');

            // Връзка с клиент
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            // Основни идентификатори
            $table->string('vin')->nullable()->comment('Шаси номер (VIN)');
            $table->string('chassis')->nullable()->comment('Шаси (от Access)');
            $table->string('plate')->nullable()->comment('Регистрационен номер');
            $table->string('dk_no')->nullable()->comment('ДК номер');

            // Марка и модел (може да се комбинират от Access поле 'Автомобил')
            $table->string('make')->nullable()->comment('Марка');
            $table->string('model')->nullable()->comment('Модел');

            // Допълнителни данни от Access
            $table->year('year')->nullable()->comment('Година на производство');
            $table->unsignedInteger('mileage')->nullable()->comment('Пробег в километри');
            $table->string('monitor_code')->nullable()->comment('Код на монитора (от Access)');

            // Метаданни за импорта от Access
            $table->string('order_reference')->nullable()->comment('Поръчка (от Access)');
            $table->date('po_date')->nullable()->comment('Дата на поръчка (PODate от Access)');
            $table->string('author')->nullable()->comment('Автор (Author от Access)');

            // Бележки и статус
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Индекси за често използвани полета
            $table->index('old_system_id');
            $table->index('import_batch');
            $table->index('plate');
            $table->index(['make', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};