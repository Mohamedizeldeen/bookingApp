<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Company;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            if ($company->company_name === 'Tech Solutions Inc') {
                // Tech company services
                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Website Development',
                    'description' => 'Complete website development from design to deployment',
                    'price' => 2500.00,
                    'duration_minutes' => 180, // 3 hours consultation
                    'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Mobile App Development',
                    'description' => 'Native and cross-platform mobile application development',
                    'price' => 5000.00,
                    'duration_minutes' => 240, // 4 hours consultation
                    'availability' => ['Monday', 'Wednesday', 'Friday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'IT Consultation',
                    'description' => 'Technical consultation for business requirements',
                    'price' => 150.00,
                    'duration_minutes' => 60,
                    'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'System Integration',
                    'description' => 'Integration of existing systems and new technologies',
                    'price' => 800.00,
                    'duration_minutes' => 120,
                    'availability' => ['Tuesday', 'Thursday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Technical Support',
                    'description' => 'Ongoing technical support and maintenance',
                    'price' => 100.00,
                    'duration_minutes' => 30,
                    'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                ]);
            }

            if ($company->company_name === 'Beauty Spa Wellness') {
                // Beauty spa services
                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Deep Tissue Massage',
                    'description' => 'Therapeutic deep tissue massage for muscle tension relief',
                    'price' => 120.00,
                    'duration_minutes' => 90,
                    'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Facial Treatment',
                    'description' => 'Rejuvenating facial treatment with premium skincare',
                    'price' => 85.00,
                    'duration_minutes' => 60,
                    'availability' => ['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Manicure & Pedicure',
                    'description' => 'Complete nail care service with luxury treatment',
                    'price' => 65.00,
                    'duration_minutes' => 75,
                    'availability' => ['Monday', 'Wednesday', 'Friday', 'Saturday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Hair Styling',
                    'description' => 'Professional hair cut, color, and styling',
                    'price' => 95.00,
                    'duration_minutes' => 120,
                    'availability' => ['Tuesday', 'Thursday', 'Friday', 'Saturday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Body Wrap Therapy',
                    'description' => 'Detoxifying body wrap with natural ingredients',
                    'price' => 140.00,
                    'duration_minutes' => 100,
                    'availability' => ['Wednesday', 'Thursday', 'Saturday'],
                ]);
            }

            if ($company->company_name === 'HealthCare Clinic') {
                // Healthcare services
                Service::create([
                    'company_id' => $company->id,
                    'name' => 'General Consultation',
                    'description' => 'Comprehensive medical examination and consultation',
                    'price' => 150.00,
                    'duration_minutes' => 30,
                    'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Specialist Consultation',
                    'description' => 'Specialized medical consultation with expert doctors',
                    'price' => 250.00,
                    'duration_minutes' => 45,
                    'availability' => ['Tuesday', 'Wednesday', 'Thursday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Health Screening',
                    'description' => 'Complete health screening and diagnostic tests',
                    'price' => 300.00,
                    'duration_minutes' => 120,
                    'availability' => ['Monday', 'Wednesday', 'Friday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Vaccination Service',
                    'description' => 'Immunization and vaccination services',
                    'price' => 50.00,
                    'duration_minutes' => 15,
                    'availability' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                ]);

                Service::create([
                    'company_id' => $company->id,
                    'name' => 'Physical Therapy',
                    'description' => 'Rehabilitation and physical therapy sessions',
                    'price' => 100.00,
                    'duration_minutes' => 60,
                    'availability' => ['Tuesday', 'Thursday', 'Friday'],
                ]);
            }
        }
    }
}
