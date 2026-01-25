<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Връзка със стария Access
            $table->string('old_id')->nullable()->index();
            $table->string('customer_number')->nullable()->index();

            // Основни данни
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();

            // Адреси
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->string('res_address_1')->nullable();
            $table->string('res_address_2')->nullable();

            // Юридически данни
            $table->string('mol')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('bulstat')->nullable();
            $table->string('bulstat_letter')->nullable();
            $table->string('doc_type')->nullable();

            // Получател
            $table->string('receiver')->nullable();
            $table->text('receiver_details')->nullable();

            // Допълнителни
            $table->date('eidate')->nullable();
            $table->string('partida')->nullable();
            $table->text('notes')->nullable();

            // Флагове
            $table->boolean('include_in_mailing')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_customer')->default(true);
            $table->boolean('is_supplier')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
