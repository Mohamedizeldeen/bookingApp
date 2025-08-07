<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the authenticated user
     */
    public function getUnread()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('company_id', $user->company_id)
            ->when($user->role !== 'admin', function ($query) use ($user) {
                // For staff, only show notifications assigned to them or general notifications
                return $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereNull('user_id'); // General notifications for everyone
                });
            })
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        // Ensure user can only mark their own company's notifications as read
        if ($notification->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the current user
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $query = Notification::where('company_id', $user->company_id)
            ->unread();
            
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereNull('user_id');
            });
        }
        
        $query->update([
            'read_at' => now(),
            'status' => 'read'
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Create a new notification
     */
    public static function createNotification($data)
    {
        return Notification::create([
            'company_id' => $data['company_id'],
            'user_id' => $data['user_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'appointment_id' => $data['appointment_id'] ?? null,
            'type' => $data['type'] ?? 'info',
            'event' => $data['event'] ?? 'general',
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'status' => 'pending'
        ]);
    }
}
