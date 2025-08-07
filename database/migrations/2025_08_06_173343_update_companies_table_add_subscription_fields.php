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
        Schema::table('companies', function (Blueprint $table) {
            $table->enum('subscription_status', ['active', 'blocked', 'pending', 'expired'])->default('pending');
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->decimal('monthly_fee', 8, 2)->default(0.00);
            $table->date('last_payment_date')->nullable();
            $table->date('next_payment_due')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->text('block_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_status',
                'subscription_start_date', 
                'subscription_end_date',
                'monthly_fee',
                'last_payment_date',
                'next_payment_due',
                'is_blocked',
                'block_reason',
                'blocked_at'
            ]);
        });
    }
};
