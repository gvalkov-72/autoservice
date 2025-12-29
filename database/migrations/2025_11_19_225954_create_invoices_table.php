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
            $table->id();
            
            // Връзка с клиенти
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // Основна информация
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            
            // Допълнителни дати от Access
            $table->date('received_date')->nullable();
            $table->date('date_due')->nullable();
            
            // Статуси
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'voided', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            
            // Тип фактура от Access
            $table->string('invoice_type')->default('standard')->comment('Invoice-Type от Access');
            $table->string('sale_type')->nullable()->comment('saletype от Access');
            
            // Суми
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            
            // Плащания от Access
            $table->decimal('payment_cash', 12, 2)->default(0)->comment('paymentcash от Access');
            $table->string('payment_iod')->nullable()->comment('paymentiod от Access');
            
            // Флагове от Access
            $table->boolean('is_void')->default(false)->comment('void от Access');
            $table->boolean('is_printed')->default(false)->comment('Printed от Access');
            $table->boolean('is_paid')->default(false)->comment('Paid от Access');
            
            // Отговорности от Access
            $table->string('received_person')->nullable()->comment('Invoice-Received-Person от Access');
            $table->string('invoice_rec_responsible')->nullable()->comment('invoiceRecResponsible от Access');
            $table->string('invoice_cre_responsible')->nullable()->comment('invoiceCreResponsible от Access');
            
            // Допълнителна информация
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('zero_explain')->nullable()->comment('zeroexplain от Access');
            $table->text('additional_info')->nullable()->comment('Invoice-Kiúrôaâée от Access');
            $table->string('tips_deka')->nullable()->comment('tipsdeka от Access');
            
            // Soft deletes
            $table->softDeletes();
            $table->timestamps();
            
            // Индекси за по-бързо търсене
            $table->index('invoice_number');
            $table->index('customer_id');
            $table->index('invoice_date');
            $table->index('status');
            $table->index('payment_status');
            $table->index('is_paid');
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