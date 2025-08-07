<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a notification for appointment assignment
     */
    public static function appointmentAssigned(Appointment $appointment)
    {
        if (!$appointment->assignedUser) return;

        return Notification::create([
            'company_id' => $appointment->company_id,
            'user_id' => $appointment->assigned_user_id,
            'appointment_id' => $appointment->id,
            'type' => 'in_app',
            'event' => 'appointment_assigned',
            'title' => 'Appointment Assigned to You',
            'message' => "You have been assigned an appointment with {$appointment->customer->name} for {$appointment->service->name} on " . $appointment->appointment_date->format('M j, Y g:i A'),
            'status' => 'sent',
        ]);
    }

    /**
     * Create a notification for appointment confirmation
     */
    public static function appointmentConfirmed(Appointment $appointment)
    {
        $notifications = [];

        // Notify the assigned user
        if ($appointment->assignedUser) {
            $notifications[] = Notification::create([
                'company_id' => $appointment->company_id,
                'user_id' => $appointment->assigned_user_id,
                'appointment_id' => $appointment->id,
                'type' => 'in_app',
                'event' => 'appointment_confirmed',
                'title' => 'Appointment Confirmed',
                'message' => "Appointment with {$appointment->customer->name} has been confirmed",
                'status' => 'sent',
            ]);
        }

        // Notify admin if confirmation was done by staff
        $company = $appointment->company;
        $admin = $company->users()->where('role', 'admin')->first();
        if ($admin && $admin->id !== $appointment->assigned_user_id) {
            $notifications[] = Notification::create([
                'company_id' => $appointment->company_id,
                'user_id' => $admin->id,
                'appointment_id' => $appointment->id,
                'type' => 'in_app',
                'event' => 'appointment_confirmed',
                'title' => 'Appointment Confirmed by Staff',
                'message' => "{$appointment->assignedUser->name} confirmed appointment with {$appointment->customer->name}",
                'status' => 'sent',
            ]);
        }

        return $notifications;
    }

    /**
     * Create a notification for appointment completion
     */
    public static function appointmentCompleted(Appointment $appointment)
    {
        $notifications = [];

        // Notify the assigned user
        if ($appointment->assignedUser) {
            $notifications[] = Notification::create([
                'company_id' => $appointment->company_id,
                'user_id' => $appointment->assigned_user_id,
                'appointment_id' => $appointment->id,
                'type' => 'in_app',
                'event' => 'appointment_completed',
                'title' => 'Appointment Completed',
                'message' => "Appointment with {$appointment->customer->name} has been completed",
                'status' => 'sent',
            ]);
        }

        // Notify admin
        $company = $appointment->company;
        $admin = $company->users()->where('role', 'admin')->first();
        if ($admin && $admin->id !== $appointment->assigned_user_id) {
            $notifications[] = Notification::create([
                'company_id' => $appointment->company_id,
                'user_id' => $admin->id,
                'appointment_id' => $appointment->id,
                'type' => 'in_app',
                'event' => 'appointment_completed',
                'title' => 'Appointment Completed by Staff',
                'message' => "{$appointment->assignedUser->name} completed appointment with {$appointment->customer->name}",
                'status' => 'sent',
            ]);
        }

        return $notifications;
    }

    /**
     * Create a notification for appointment cancellation
     */
    public static function appointmentCancelled(Appointment $appointment)
    {
        $notifications = [];

        // Notify the assigned user
        if ($appointment->assignedUser) {
            $notifications[] = Notification::create([
                'company_id' => $appointment->company_id,
                'user_id' => $appointment->assigned_user_id,
                'appointment_id' => $appointment->id,
                'type' => 'in_app',
                'event' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled',
                'message' => "Appointment with {$appointment->customer->name} has been cancelled",
                'status' => 'sent',
            ]);
        }

        // Notify admin if cancellation was done by staff
        $company = $appointment->company;
        $admin = $company->users()->where('role', 'admin')->first();
        if ($admin && $admin->id !== $appointment->assigned_user_id) {
            $notifications[] = Notification::create([
                'company_id' => $appointment->company_id,
                'user_id' => $admin->id,
                'appointment_id' => $appointment->id,
                'type' => 'in_app',
                'event' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled by Staff',
                'message' => "{$appointment->assignedUser->name} cancelled appointment with {$appointment->customer->name}",
                'status' => 'sent',
            ]);
        }

        return $notifications;
    }

    /**
     * Create a notification for new online booking
     */
    public static function newOnlineBooking(Appointment $appointment)
    {
        $company = $appointment->company;
        $admin = $company->users()->where('role', 'admin')->first();
        
        if (!$admin) return null;

        return Notification::create([
            'company_id' => $appointment->company_id,
            'user_id' => $admin->id,
            'appointment_id' => $appointment->id,
            'type' => 'in_app',
            'event' => 'new_booking',
            'title' => 'New Online Booking',
            'message' => "New booking from {$appointment->customer->name} for {$appointment->service->name} on " . $appointment->appointment_date->format('M j, Y g:i A'),
            'status' => 'sent',
        ]);
    }

    /**
     * Create a notification for new appointment created internally
     */
    public static function appointmentCreated(Appointment $appointment)
    {
        if (!$appointment->assignedUser) return null;

        return Notification::create([
            'company_id' => $appointment->company_id,
            'user_id' => $appointment->assigned_user_id,
            'appointment_id' => $appointment->id,
            'type' => 'in_app',
            'event' => 'appointment_created',
            'title' => 'New Appointment Scheduled',
            'message' => "New appointment with {$appointment->customer->name} for {$appointment->service->name} on " . $appointment->appointment_date->format('M j, Y g:i A'),
            'status' => 'sent',
        ]);
    }

    /**
     * Create a general notification for all company users
     */
    public static function generalNotification($companyId, $title, $message, $event = 'general')
    {
        return Notification::create([
            'company_id' => $companyId,
            'user_id' => null, // General notification for all users
            'type' => 'in_app',
            'event' => $event,
            'title' => $title,
            'message' => $message,
            'status' => 'sent',
        ]);
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount(User $user)
    {
        return Notification::where('company_id', $user->company_id)
            ->when($user->role !== 'admin', function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereNull('user_id');
                });
            })
            ->unread()
            ->count();
    }

    /**
     * Create appointment reminder notifications
     */
    public static function sendAppointmentReminders()
    {
        // Get appointments starting in the next 30 minutes
        $upcomingAppointments = Appointment::where('status', 'confirmed')
            ->where('appointment_date', '>=', Carbon::now())
            ->where('appointment_date', '<=', Carbon::now()->addMinutes(30))
            ->with(['customer', 'service', 'assignedUser'])
            ->get();

        $notifications = [];

        foreach ($upcomingAppointments as $appointment) {
            if ($appointment->assignedUser) {
                $notifications[] = Notification::create([
                    'company_id' => $appointment->company_id,
                    'user_id' => $appointment->assigned_user_id,
                    'appointment_id' => $appointment->id,
                    'type' => 'in_app',
                    'event' => 'appointment_reminder',
                    'title' => 'Upcoming Appointment',
                    'message' => "Reminder: You have an appointment with {$appointment->customer->name} starting soon",
                    'status' => 'sent',
                ]);
            }
        }

        return $notifications;
    }
}
