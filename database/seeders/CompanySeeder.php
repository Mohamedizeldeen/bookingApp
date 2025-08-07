<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Company 1: Tech Solutions Inc
        $company1 = Company::create([
            'company_name' => 'Tech Solutions Inc',
            'phone' => '+1-555-0123',
            'type_of_subscription' => 'professional',
        ]);

        // Admin user for company 1
        $admin1 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@techsolutions.com',
            'password' => Hash::make('123456'),
            'company_id' => $company1->id,
            'role' => 'admin',
        ]);

        // Update company with admin user_id
        $company1->update(['user_id' => $admin1->id]);

        // 3 staff users for company 1
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@techsolutions.com',
            'password' => Hash::make('123456'),
            'company_id' => $company1->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@techsolutions.com',
            'password' => Hash::make('123456'),
            'company_id' => $company1->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike.johnson@techsolutions.com',
            'password' => Hash::make('123456'),
            'company_id' => $company1->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Emily Chen',
            'email' => 'emily@techsolutions.com',
            'password' => Hash::make('123456'),
            'company_id' => $company1->id,
            'role' => 'user',
        ]);

        // Company 2: Beauty Spa Wellness
        $company2 = Company::create([
            'company_name' => 'Beauty Spa Wellness',
            'phone' => '+1-555-0456',
            'type_of_subscription' => 'starter',
        ]);

        // Admin user for company 2
        $admin2 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@beautyspa.com',
            'password' => Hash::make('123456'),
            'company_id' => $company2->id,
            'role' => 'admin',
        ]);

        // Update company with admin user_id
        $company2->update(['user_id' => $admin2->id]);

        // 3 staff users for company 2
        User::create([
            'name' => 'Sarah Wilson',
            'email' => 'sarah.wilson@beautyspa.com',
            'password' => Hash::make('123456'),
            'company_id' => $company2->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Emily Brown',
            'email' => 'emily.brown@beautyspa.com',
            'password' => Hash::make('123456'),
            'company_id' => $company2->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Lisa Davis',
            'email' => 'lisa.davis@beautyspa.com',
            'password' => Hash::make('123456'),
            'company_id' => $company2->id,
            'role' => 'user',
        ]);

        // Company 3: HealthCare Clinic
        $company3 = Company::create([
            'company_name' => 'HealthCare Clinic',
            'phone' => '+1-555-0789',
            'type_of_subscription' => 'enterprise',
        ]);

        // Admin user for company 3
        $admin3 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@healthcareclinic.com',
            'password' => Hash::make('123456'),
            'company_id' => $company3->id,
            'role' => 'admin',
        ]);

        // Update company with admin user_id
        $company3->update(['user_id' => $admin3->id]);

        // 3 staff users for company 3
        User::create([
            'name' => 'Dr. Miller',
            'email' => 'dr.miller@healthcareclinic.com',
            'password' => Hash::make('123456'),
            'company_id' => $company3->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Nurse Garcia',
            'email' => 'nurse.garcia@healthcareclinic.com',
            'password' => Hash::make('123456'),
            'company_id' => $company3->id,
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Dr. Martinez',
            'email' => 'dr.martinez@healthcareclinic.com',
            'password' => Hash::make('123456'),
            'company_id' => $company3->id,
            'role' => 'user',
        ]);
    }
}
