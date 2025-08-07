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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->string('calendar_event_id')->nullable(); // Store external calendar event ID
            $table->timestamp('synced_at')->nullable(); // Last sync timestamp
            $table->json('sync_metadata')->nullable(); // Store sync-related data
            
            $table->index(['location_id']);
            $table->index(['calendar_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['location_id', 'calendar_event_id', 'synced_at', 'sync_metadata']);
        });
    }
};
