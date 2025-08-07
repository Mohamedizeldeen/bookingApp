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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Who receives the notification
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade'); // Related customer
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade'); // Related appointment
            $table->string('type'); // email, sms, push, in_app
            $table->string('event'); // appointment_reminder, appointment_confirmed, appointment_cancelled, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data as JSON
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->timestamp('scheduled_at')->nullable(); // When to send the notification
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['company_id', 'status']);
            $table->index(['user_id', 'read_at']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
