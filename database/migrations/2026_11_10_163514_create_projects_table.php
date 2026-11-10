<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Sales Officer & VA assignments (foreign keys)
            $table->foreignId('sales_officer_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Financial fields (using decimal for exact precision)
            $table->decimal('project_amount', 12, 2);
            $table->enum('payment_type', ['one-time', 'recurring']);
            
            // Commission percentages snapshot / overrides for this specific project
            $table->decimal('va_commission_percentage', 5, 2)->default(10.00);
            $table->decimal('sales_commission_percentage', 5, 2)->default(5.00);
            
            $table->enum('status', ['pending', 'active', 'on_hold', 'completed', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};