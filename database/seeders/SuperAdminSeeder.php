<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create SaaS Owner (Super Admin)
        User::create([
            'name' => 'Mohamed Izeldeen',
            'email' => 'eng.mohamed.izeldeen@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'super_admin',
            'company_id' => null, // Super admin doesn't belong to any company
            'email_verified_at' => now(),
        ]);

        // Create Employee for subscription checking
        User::create([
            'name' => 'Subscription Manager',
            'email' => 'subscriptions@bookingapp.com',
            'password' => Hash::make('123456'),
            'role' => 'employee',
            'company_id' => null, // Employee doesn't belong to any company
            'email_verified_at' => now(),
        ]);
    }
}
