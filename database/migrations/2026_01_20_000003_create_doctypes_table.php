<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctypes', function (Blueprint $table) {
            $table->unsignedInteger('type')->primary(); // Access PK

            $table->string('name');
            $table->string('short', 20)->nullable();

            $table->string('ddstype', 10)->nullable();
            $table->unsignedTinyInteger('ajurtype')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctypes');
    }
};
