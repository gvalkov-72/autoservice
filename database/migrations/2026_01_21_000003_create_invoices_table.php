<?php

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
        Schema::create('invoices', function (Blueprint $table) {

            // Laravel primary key
            $table->id();

            // ===== Access identifiers =====
            // Invoice-ID
            $table->unsignedInteger('old_id')->unique();

            // Customer-ID (връзката ще се прави по-късно)
            $table->unsignedInteger('customer_old_id')->index();

            // Invoice-Type (doctypes.type, БЕЗ FK)
            $table->unsignedSmallInteger('invoice_type')->index();

            // ===== Dates =====
            // Invoice-Date
            $table->date('invoice_date')->nullable();

            // Invoice-Received-Date
            $table->date('invoice_received_date')->nullable();

            // datedue
            $table->date('date_due')->nullable();

            // ===== Persons / Text fields =====
            // Invoice-Received-Person
            $table->string('invoice_received_person')->nullable();

            // Invoice-Съставил
            $table->string('invoice_created_by')->nullable();

            // InvoiceRecResponsible
            $table->string('invoice_rec_responsible')->nullable();

            // InvoiceCreResponsible
            $table->string('invoice_cre_responsible')->nullable();

            // ===== Notes =====
            // Note
            $table->text('note')->nullable();

            // zeroexplain
            $table->text('zeroexplain')->nullable();

            // ===== Boolean flags (Да / Не) =====
            // paymentcash
            $table->boolean('payment_cash')->default(false);

            // void
            $table->boolean('is_void')->default(false);

            // Printed
            $table->boolean('printed')->default(false);

            // Paid
            $table->boolean('paid')->default(false);

            // ===== Numeric fields =====
            // tipsdelka
            $table->unsignedTinyInteger('tipsdelka')->default(0);

            // saletype
            $table->unsignedTinyInteger('sale_type')->default(0);

            // paymethod
            $table->unsignedTinyInteger('pay_method')->default(0);

            // ===== Timestamps =====
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
