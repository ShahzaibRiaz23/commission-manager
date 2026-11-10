<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('recurring_payment_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('payment_amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_type'); // 'one-time' or 'recurring'
            
            // Snapshot percentages at the time of payment
            $table->decimal('va_commission_percentage', 5, 2);
            $table->decimal('va_commission_amount', 12, 2);
            
            $table->decimal('sales_commission_percentage', 5, 2);
            $table->decimal('sales_commission_amount', 12, 2);
            
            $table->decimal('remaining_revenue', 12, 2);
            
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->string('transaction_reference')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_payments');
    }
};