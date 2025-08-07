<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $customers = Customer::where('company_id', $company->id)->get();
            $services = Service::where('company_id', $company->id)->get();
            $users = User::where('company_id', $company->id)->where('role', 'user')->get();
            $admin = User::where('company_id', $company->id)->where('role', 'admin')->first();

            // Skip if no users or services available
            if ($users->isEmpty() || $services->isEmpty() || $customers->isEmpty()) {
                continue;
            }

            // Create 15-20 appointments per company
            for ($i = 0; $i < rand(15, 20); $i++) {
                $customer = $customers->random();
                $service = $services->random();
                $assignedUser = $users->random();
                
                // Random appointment date (past, present, future)
                $appointmentDate = Carbon::now()->addDays(rand(-30, 60))->setHour(rand(9, 17))->setMinute([0, 15, 30, 45][rand(0, 3)]);
                $endTime = $appointmentDate->copy()->addMinutes($service->duration_minutes);
                
                $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled'];
                $status = $statuses[rand(0, 3)];
                
                // Adjust status based on date
                if ($appointmentDate->isPast()) {
                    $status = ['completed', 'cancelled', 'no_show'][rand(0, 2)];
                } elseif ($appointmentDate->isToday() || $appointmentDate->isTomorrow()) {
                    $status = ['scheduled', 'confirmed'][rand(0, 1)];
                }

                Appointment::create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'assigned_user_id' => $assignedUser->id,
                    'created_by' => $admin->id,
                    'appointment_date' => $appointmentDate,
                    'end_time' => $endTime,
                    'status' => $status,
                    'price' => $service->price * (rand(80, 120) / 100), // Price variation ±20%
                    'notes' => $this->generateRandomNotes($status),
                    'reminder_settings' => [
                        'email' => true,
                        'sms' => true,
                        'hours_before' => [24, 2],
                    ],
                    'reminder_sent_at' => $appointmentDate->isPast() ? $appointmentDate->subHours(24) : null,
                ]);
            }
        }
    }

    private function generateRandomNotes($status)
    {
        $notes = [
            'scheduled' => [
                'First-time customer',
                'Requested specific time slot',
                'Customer has allergies - check notes',
                'Prefers morning appointments',
            ],
            'confirmed' => [
                'Confirmation sent via email',
                'Customer confirmed via phone',
                'Special requests noted',
                'Regular customer',
            ],
            'completed' => [
                'Service completed successfully',
                'Customer satisfied with service',
                'Follow-up scheduled',
                'Excellent feedback received',
            ],
            'cancelled' => [
                'Customer cancelled due to emergency',
                'Rescheduled to next week',
                'Weather-related cancellation',
                'Personal reasons',
            ],
            'no_show' => [
                'Customer did not show up',
                'No response to calls',
                'Will follow up tomorrow',
                'Added to no-show list',
            ],
        ];

        $statusNotes = $notes[$status] ?? ['General appointment notes'];
        return $statusNotes[array_rand($statusNotes)];
    }
}
