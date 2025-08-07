<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $appointments = Appointment::where('company_id', $company->id)->get();
            $users = User::where('company_id', $company->id)->get();

            foreach ($appointments as $appointment) {
                // Create appointment confirmation notification
                Notification::create([
                    'company_id' => $company->id,
                    'user_id' => $appointment->assigned_user_id,
                    'customer_id' => $appointment->customer_id,
                    'appointment_id' => $appointment->id,
                    'type' => 'email',
                    'event' => 'appointment_confirmed',
                    'title' => 'Appointment Confirmed',
                    'message' => "Your appointment for {$appointment->service->name} has been confirmed for " . $appointment->appointment_date->format('M j, Y g:i A'),
                    'data' => [
                        'service_name' => $appointment->service->name,
                        'appointment_date' => $appointment->appointment_date->toISOString(),
                        'customer_name' => $appointment->customer->name,
                    ],
                    'status' => 'sent',
                    'scheduled_at' => $appointment->created_at,
                    'sent_at' => $appointment->created_at->addMinutes(5),
                ]);

                // Create reminder notification (24 hours before)
                if ($appointment->appointment_date->isFuture()) {
                    Notification::create([
                        'company_id' => $company->id,
                        'user_id' => null,
                        'customer_id' => $appointment->customer_id,
                        'appointment_id' => $appointment->id,
                        'type' => 'sms',
                        'event' => 'appointment_reminder',
                        'title' => 'Appointment Reminder',
                        'message' => "Reminder: You have an appointment tomorrow at " . $appointment->appointment_date->format('g:i A') . " for {$appointment->service->name}",
                        'data' => [
                            'service_name' => $appointment->service->name,
                            'appointment_date' => $appointment->appointment_date->toISOString(),
                            'company_name' => $company->company_name,
                        ],
                        'status' => 'pending',
                        'scheduled_at' => $appointment->appointment_date->subHours(24),
                    ]);
                }

                // Random staff notifications
                if (rand(1, 3) === 1) {
                    $randomUser = $users->random();
                    Notification::create([
                        'company_id' => $company->id,
                        'user_id' => $randomUser->id,
                        'customer_id' => null,
                        'appointment_id' => $appointment->id,
                        'type' => 'in_app',
                        'event' => 'appointment_assigned',
                        'title' => 'New Appointment Assigned',
                        'message' => "You have been assigned to an appointment with {$appointment->customer->name}",
                        'data' => [
                            'customer_name' => $appointment->customer->name,
                            'service_name' => $appointment->service->name,
                            'appointment_date' => $appointment->appointment_date->toISOString(),
                        ],
                        'status' => rand(0, 1) ? 'read' : 'sent',
                        'scheduled_at' => $appointment->created_at,
                        'sent_at' => $appointment->created_at->addMinutes(2),
                        'read_at' => rand(0, 1) ? $appointment->created_at->addHours(rand(1, 24)) : null,
                    ]);
                }
            }

            // Create some general company notifications
            $admin = User::where('company_id', $company->id)->where('role', 'admin')->first();
            
            // Monthly report notification
            Notification::create([
                'company_id' => $company->id,
                'user_id' => $admin->id,
                'customer_id' => null,
                'appointment_id' => null,
                'type' => 'email',
                'event' => 'monthly_report',
                'title' => 'Monthly Business Report',
                'message' => 'Your monthly business report is ready for review',
                'data' => [
                    'report_month' => Carbon::now()->subMonth()->format('F Y'),
                    'total_appointments' => $appointments->count(),
                    'revenue' => $appointments->sum('price'),
                ],
                'status' => 'sent',
                'scheduled_at' => Carbon::now()->startOfMonth(),
                'sent_at' => Carbon::now()->startOfMonth()->addHours(9),
                'read_at' => Carbon::now()->startOfMonth()->addHours(10),
            ]);

            // System update notification
            Notification::create([
                'company_id' => $company->id,
                'user_id' => $admin->id,
                'customer_id' => null,
                'appointment_id' => null,
                'type' => 'in_app',
                'event' => 'system_update',
                'title' => 'System Update Available',
                'message' => 'New features are available in your BookingApp dashboard',
                'data' => [
                    'update_version' => '2.1.0',
                    'features' => ['Improved calendar view', 'New reporting tools', 'Enhanced notifications'],
                ],
                'status' => 'sent',
                'scheduled_at' => Carbon::now()->subDays(3),
                'sent_at' => Carbon::now()->subDays(3),
            ]);
        }
    }
}
