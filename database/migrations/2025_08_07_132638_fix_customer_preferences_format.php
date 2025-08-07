<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix any customer preferences that might be stored as strings
        $customers = Customer::all();
        
        foreach ($customers as $customer) {
            // Get the raw attribute value without casting
            $rawPreferences = $customer->getAttributes()['preferences'] ?? null;
            
            if ($rawPreferences && is_string($rawPreferences)) {
                // Convert string to array if it's a comma-separated string
                if (str_contains($rawPreferences, ',')) {
                    $preferencesArray = array_map('trim', explode(',', $rawPreferences));
                } else {
                    // Single preference
                    $preferencesArray = [trim($rawPreferences)];
                }
                
                $customer->update(['preferences' => $preferencesArray]);
            } elseif (is_null($rawPreferences)) {
                // Set null preferences to empty array
                $customer->update(['preferences' => []]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert arrays back to strings if needed
        $customers = Customer::all();
        
        foreach ($customers as $customer) {
            if ($customer->preferences && is_array($customer->preferences)) {
                $preferencesString = implode(', ', $customer->preferences);
                $customer->update(['preferences' => $preferencesString]);
            }
        }
    }
};
