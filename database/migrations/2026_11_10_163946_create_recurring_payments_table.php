<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('recurring_amount', 12, 2);
            $table->enum('billing_frequency', ['weekly', 'monthly', 'quarterly', 'annually'])->default('monthly');
            $table->date('recurring_start_date');
            $table->date('next_payment_date');
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_payments');
    }
};