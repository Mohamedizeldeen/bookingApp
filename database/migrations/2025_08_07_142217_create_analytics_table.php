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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('metric_type'); // 'appointments', 'revenue', 'customers', 'staff_performance', etc.
            $table->string('metric_name'); // 'total_appointments', 'completed_appointments', 'cancelled_appointments', etc.
            $table->decimal('value', 15, 2);
            $table->json('metadata')->nullable(); // Additional data like location_id, service_id, staff_id
            $table->timestamps();

            $table->unique(['company_id', 'date', 'metric_type', 'metric_name']);
            $table->index(['company_id', 'date']);
            $table->index(['metric_type', 'metric_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
