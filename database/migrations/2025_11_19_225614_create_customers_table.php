<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('old_system_id')->nullable()->comment('ID от старата Access система');
            
            // Основна информация
            $table->string('type')->default('customer')->comment('customer, supplier, both');
            $table->string('name');
            $table->string('vat_number')->nullable();
            $table->string('bulstat')->nullable()->comment('Булстат без BG префикс');
            $table->string('contact_person')->nullable();
            
            // Контакти
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            
            // Адрес
            $table->text('address')->nullable()->comment('Основен адрес (комбиниран)');
            $table->string('address_line1')->nullable()->comment('Адрес ред 1 от старата система');
            $table->string('address_line2')->nullable()->comment('Адрес ред 2 от старата система');
            $table->string('city')->nullable();
            
            // Допълнителна информация
            $table->text('notes')->nullable();
            $table->string('court_registration')->nullable()->comment('Част от регистрация (partida)');
            $table->string('bulstat_letter')->nullable()->comment('Буква от булстат');
            
            // Флагове от старата система
            $table->boolean('is_active')->default(true)->comment('Активен клиент');
            $table->boolean('include_in_reports')->default(true)->comment('Включване в справки');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};