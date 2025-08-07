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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null'); // Staff member assigned
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Who created the appointment
            $table->dateTime('appointment_date');
            $table->dateTime('end_time');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->decimal('price', 10, 2)->nullable(); // Can override service price
            $table->text('notes')->nullable();
            $table->json('reminder_settings')->nullable(); // Store reminder preferences
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['company_id', 'appointment_date']);
            $table->index(['customer_id', 'appointment_date']);
            $table->index(['assigned_user_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
