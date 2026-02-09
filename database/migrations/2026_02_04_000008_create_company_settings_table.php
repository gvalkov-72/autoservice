<?php
// database/migrations/2024_01_15_000000_create_company_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('iban')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bic')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Първи запис по подразбиране
        DB::table('company_settings')->insert([
            'name' => 'ВАШАТА КОМПАНИЯ АД',
            'city' => 'ВАШИЯТ ГРАД',
            'address' => 'ул. ВАШАТА АДРЕС',
            'vat_number' => '000000000',
            'contact_person' => 'ВАШЕТО ИМЕ',
            'iban' => 'BG00XXXX00000000000000',
            'bank_name' => 'ВАШАТА БАНКА АД',
            'bic' => 'XXXXXXXX',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};