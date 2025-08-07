<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Company;
use App\Models\Appointment;
use Carbon\Carbon;

class NotificationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a company and its users
        $company = Company::first();
        if (!$company) return;

        $users = $company->users;
        if ($users->isEmpty()) return;

        $admin = $users->where('role', 'admin')->first();
        $staff = $users->where('role', 'user')->first();

        // Create test notifications for admin
        if ($admin) {
            // New booking notification
            Notification::create([
                'company_id' => $company->id,
                'user_id' => $admin->id,
                'type' => 'in_app',
                'event' => 'new_booking',
                'title' => 'New Online Booking',
                'message' => 'New booking from John Doe for Hair Cut on Dec 15, 2024 2:00 PM',
                'status' => 'sent',
                'created_at' => Carbon::now()->subMinutes(5),
            ]);

            // Staff task completion
            Notification::create([
                'company_id' => $company->id,
                'user_id' => $admin->id,
                'type' => 'in_app',
                'event' => 'appointment_completed',
                'title' => 'Appointment Completed',
                'message' => 'Sarah completed an appointment with Jane Smith',
                'status' => 'sent',
                'created_at' => Carbon::now()->subMinutes(15),
            ]);

            // General company notification
            Notification::create([
                'company_id' => $company->id,
                'user_id' => null, // General notification for everyone
                'type' => 'in_app',
                'event' => 'general',
                'title' => 'System Update',
                'message' => 'New features have been added to your dashboard',
                'status' => 'sent',
                'created_at' => Carbon::now()->subHours(2),
            ]);
        }

        // Create test notifications for staff
        if ($staff) {
            // Assignment notification
            Notification::create([
                'company_id' => $company->id,
                'user_id' => $staff->id,
                'type' => 'in_app',
                'event' => 'appointment_assigned',
                'title' => 'Appointment Assigned to You',
                'message' => 'You have been assigned an appointment with Mike Johnson for Consultation on Dec 16, 2024 10:00 AM',
                'status' => 'sent',
                'created_at' => Carbon::now()->subMinutes(10),
            ]);

            // Upcoming appointment reminder
            Notification::create([
                'company_id' => $company->id,
                'user_id' => $staff->id,
                'type' => 'in_app',
                'event' => 'appointment_reminder',
                'title' => 'Upcoming Appointment',
                'message' => 'Reminder: You have an appointment with Lisa Brown in 30 minutes',
                'status' => 'sent',
                'created_at' => Carbon::now()->subMinutes(30),
            ]);
        }

        $this->command->info('Test notifications created successfully!');
    }
}
