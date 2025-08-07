<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run all seeders in the correct order
        $this->call([
            CompanySeeder::class,
            ServiceSeeder::class,
            CustomerSeeder::class,
            AppointmentSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
