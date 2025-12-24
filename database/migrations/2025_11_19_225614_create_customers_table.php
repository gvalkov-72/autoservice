<?php
// database/migrations/2025_11_19_225614_create_customers_table.php
// ПЪЛЕН АКТУАЛИЗИРАН ФАЙЛ

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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            // Поле за миграция от Access (старо ID)
            $table->string('old_id')->nullable()->unique()->comment('ID от стария Access софтуер');
            $table->string('customer_number')->nullable()->unique()->comment('Номер на клиента от Access');
            
            // Основни данни за клиента
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable()->comment('Факс номер');
            
            // Адреси от Access (основен адрес)
            $table->text('address')->nullable()->comment('Основен адрес ред 1 (Customer-Address-1)');
            $table->string('address_2')->nullable()->comment('Основен адрес ред 2 (Customer-Address-2)');
            
            // Резервен адрес от Access
            $table->string('res_address_1')->nullable()->comment('Резервен адрес 1 (ResAddress1)');
            $table->string('res_address_2')->nullable()->comment('Резервен адрес 2 (ResAddress2)');
            
            // Юридически данни и контакти
            $table->string('contact_person')->nullable()->comment('Контактно лице (Contact)');
            $table->string('mol')->nullable()->comment('МОЛ - Материално отговорно лице (Customer-MOL)');
            $table->string('tax_number')->nullable()->comment('Данъчен номер (Customer-Taxno)');
            $table->string('bulstat')->nullable()->comment('Булстат (Customer-Bulstat)');
            $table->string('doc_type')->nullable()->comment('Вид документ (Customer-DocType)');
            
            // Получател информация от Access
            $table->string('receiver')->nullable()->comment('Получател (Receiver)');
            $table->text('receiver_details')->nullable()->comment('Детайли за получателя (Receiver Details)');
            
            // Допълнителни полета от Access
            $table->string('eidale')->nullable()->comment('ЕИДАЛЕ код (eidale)');
            $table->boolean('include_in_mailing')->default(true)->comment('Включване в бюлетин (include)');
            $table->string('partida')->nullable()->comment('Партида (partida)');
            $table->string('bulsial_letter')->nullable()->comment('Буква към булстат (bulsialletter)');
            
            // Флагове от Access
            $table->boolean('is_active')->default(true)->comment('Активен клиент (active)');
            $table->boolean('is_customer')->default(true)->comment('Клиент (customer)');
            $table->boolean('is_supplier')->default(false)->comment('Доставчик (supplier)');
            
            // Бележки
            $table->text('notes')->nullable()->comment('Бележки (Note)');
            
            // Timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Индекси за бързо търсене
            $table->index('old_id');
            $table->index('customer_number');
            $table->index('name');
            $table->index('tax_number');
            $table->index('bulstat');
            $table->index('phone');
            $table->index('email');
            $table->index('is_active');
            $table->index(['is_customer', 'is_supplier']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};