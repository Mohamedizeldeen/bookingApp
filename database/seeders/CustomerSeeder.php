<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Company;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        $customerData = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@email.com',
                'phone' => '+1-555-1001',
                'date_of_birth' => Carbon::parse('1985-03-15'),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob.smith@email.com',
                'phone' => '+1-555-1002',
                'date_of_birth' => Carbon::parse('1990-07-22'),
            ],
            [
                'name' => 'Carol Davis',
                'email' => 'carol.davis@email.com',
                'phone' => '+1-555-1003',
                'date_of_birth' => Carbon::parse('1988-11-08'),
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@email.com',
                'phone' => '+1-555-1004',
                'date_of_birth' => Carbon::parse('1992-05-14'),
            ],
            [
                'name' => 'Emma Brown',
                'email' => 'emma.brown@email.com',
                'phone' => '+1-555-1005',
                'date_of_birth' => Carbon::parse('1987-09-30'),
            ],
            [
                'name' => 'Frank Miller',
                'email' => 'frank.miller@email.com',
                'phone' => '+1-555-1006',
                'date_of_birth' => Carbon::parse('1983-12-03'),
            ],
            [
                'name' => 'Grace Taylor',
                'email' => 'grace.taylor@email.com',
                'phone' => '+1-555-1007',
                'date_of_birth' => Carbon::parse('1991-04-18'),
            ],
            [
                'name' => 'Henry Anderson',
                'email' => 'henry.anderson@email.com',
                'phone' => '+1-555-1008',
                'date_of_birth' => Carbon::parse('1989-08-25'),
            ],
        ];

        foreach ($companies as $company) {
            // Add 6-8 customers per company
            $customersToAdd = array_slice($customerData, 0, rand(6, 8));
            
            foreach ($customersToAdd as $index => $customerInfo) {
                Customer::create([
                    'company_id' => $company->id,
                    'name' => $customerInfo['name'],
                    'email' => $company->id . '_' . $customerInfo['email'], // Make email unique per company
                    'phone' => $customerInfo['phone'],
                    'date_of_birth' => $customerInfo['date_of_birth'],
                    'notes' => 'Regular customer since ' . Carbon::now()->subYears(rand(1, 3))->format('Y'),
                    'preferences' => [
                        'communication' => ['email', 'sms'],
                        'reminder_time' => '24_hours',
                        'preferred_days' => ['Monday', 'Wednesday', 'Friday'],
                    ],
                ]);
            }
        }
    }
}
