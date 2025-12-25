<?php
// database/migrations/2025_11_19_225616_create_products_table.php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Поле за миграция от Access
            $table->string('old_id')->nullable()->unique()->comment('ID от стария Access софтуер (PLU)');
            $table->string('plu')->nullable()->unique()->comment('PLU код от Access системата');
            
            // Основни данни
            $table->string('name');
            $table->string('code')->unique()->nullable()->comment('Вътрешен код на продукта');
            $table->text('description')->nullable();
            
            // Цени и количество
            $table->decimal('price', 12, 2)->default(0)->comment('Продажна цена');
            $table->decimal('cost_price', 12, 2)->default(0)->comment('Себестойност (acc от Access)');
            $table->decimal('quantity', 12, 3)->default(0)->comment('Налично количество (Qty от Access)');
            
            // Мерни единици
            $table->string('unit_of_measure')->default('бр.')->comment('Мерна единица (UOM от Access)');
            
            // Складови данни
            $table->string('location')->nullable()->comment('Местоположение в склада');
            $table->integer('min_stock')->default(0)->comment('Минимална наличност');
            $table->integer('max_stock')->nullable()->comment('Максимална наличност');
            
            // Баркод и допълнителна информация
            $table->string('barcode')->nullable()->unique()->comment('Баркод на продукта');
            $table->string('vendor_code')->nullable()->comment('Код на доставчика');
            $table->string('manufacturer')->nullable()->comment('Производител');
            
            // Данни от Access (допълнителни)
            $table->string('vat_rate')->nullable()->default('20%')->comment('ДДС ставка');
            $table->boolean('is_service')->default(false)->comment('Дали е услуга');
            $table->string('accounting_code')->nullable()->comment('Счетоводен код (acc от Access)');
            
            // Флагове
            $table->boolean('is_active')->default(true);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('track_stock')->default(true);
            
            // Timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Индекси
            $table->index('old_id');
            $table->index('plu');
            $table->index('code');
            $table->index('barcode');
            $table->index('name');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};