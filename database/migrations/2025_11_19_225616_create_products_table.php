<?php
// database/migrations/2025_11_19_225616_create_products_table.php
// КОРИГИРАН ФАЙЛ БЕЗ CATEGORY_ID

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
            
            // Поля за миграция от Access (старо ID)
            $table->string('old_id')->nullable()->unique()->comment('PLU код от старата Access система');
            $table->string('product_number')->nullable()->unique()->comment('Номер на продукта');
            
            // Основни данни от Access
            $table->string('sku')->unique()->comment('SKU код (може да е същия като PLU)');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            
            // Мерни единици и количество от Access
            $table->string('unit')->default('бр.')->comment('Мерна единица (UOM от Access)');
            $table->string('uom_code')->nullable()->comment('Код на мерната единица');
            $table->integer('quantity')->default(0)->comment('Количество (Qty от Access)');
            
            // Цени от Access
            $table->decimal('price', 12, 2)->comment('Продажна цена (Price от Access)');
            $table->decimal('cost_price', 12, 2)->nullable()->comment('Себестойност (acc от Access)');
            
            // Допълнителни данни
            $table->decimal('vat_percent', 5, 2)->default(20);
            $table->integer('stock_quantity')->default(0)->comment('Наличност на склад');
            $table->integer('min_stock_level')->default(0);
            $table->integer('reorder_level')->default(0)->comment('Ниво за повторна поръчка');
            
            // Локации и кодове
            $table->string('location')->nullable()->comment('Местоположение на склад');
            $table->string('barcode')->nullable()->comment('Баркод');
            $table->string('supplier_code')->nullable()->comment('Код на доставчика');
            
            // Флагове
            $table->boolean('is_active')->default(true)->comment('Активен продукт');
            $table->boolean('is_service')->default(false)->comment('Това услуга ли е?');
            $table->boolean('track_inventory')->default(true)->comment('Проследяване на инвентара');
            
            // Данни за закупки
            $table->integer('lead_time_days')->nullable()->comment('Време за доставка в дни');
            $table->decimal('last_purchase_price', 12, 2)->nullable()->comment('Последна покупна цена');
            $table->date('last_purchase_date')->nullable()->comment('Дата на последна покупка');
            
            // Timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Индекси за бързо търсене
            $table->index('old_id');
            $table->index('sku');
            $table->index('product_number');
            $table->index('name');
            $table->index('barcode');
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