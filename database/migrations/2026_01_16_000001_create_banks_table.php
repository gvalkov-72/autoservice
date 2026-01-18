<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            // ПАЗИМ Access ID
            $table->unsignedBigInteger('id')->primary();

            $table->string('account')->nullable();          // IBAN / каса
            $table->string('bank_code')->nullable();        // BankID
            $table->string('name');                         // BankName
            $table->string('currency', 3)->default('BGN');
            $table->unsignedTinyInteger('method');          // payment method
            $table->unsignedTinyInteger('type')->nullable();// cash / bank
            $table->string('short_name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
