<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name', 
        'phone',
        'contact_email',
        'address',
        'type_of_subscription',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'monthly_fee',
        'last_payment_date',
        'next_payment_due',
        'is_blocked',
        'block_reason',
        'blocked_at'
    ];

    protected $casts = [
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'last_payment_date' => 'datetime',
        'next_payment_due' => 'datetime',
        'blocked_at' => 'datetime',
        'is_blocked' => 'boolean',
        'monthly_fee' => 'decimal:2'
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function calendarIntegrations()
    {
        return $this->hasMany(CalendarIntegration::class);
    }

    public function analytics()
    {
        return $this->hasMany(Analytics::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('type_of_subscription', '!=', 'cancelled');
    }

    // Helper methods
    public function getAdminUsers()
    {
        return $this->users()->where('role', 'admin')->get();
    }

    /**
     * Get days until subscription expiry
     */
    public function getDaysUntilExpiry()
    {
        if (!$this->subscription_expires_at) {
            return null;
        }

        return now()->diffInDays($this->subscription_expires_at, false);
    }

    public function getStaffUsers()
    {
        return $this->users()->where('role', 'user')->get();
    }

    public function getAllUsers()
    {
        return $this->users()->get();
    }

    // Subscription methods
    public function isActive()
    {
        return $this->subscription_status === 'active' && !$this->is_blocked;
    }

    public function isBlocked()
    {
        return $this->is_blocked;
    }

    public function isExpired()
    {
        return $this->subscription_status === 'expired' || 
               ($this->subscription_end_date && $this->subscription_end_date->isPast());
    }

    public function isPending()
    {
        return $this->subscription_status === 'pending';
    }

    public function getMonthlyRevenue()
    {
        return $this->monthly_fee ?? 0;
    }

    public function block($reason = null)
    {
        $this->update([
            'is_blocked' => true,
            'block_reason' => $reason,
            'blocked_at' => now(),
            'subscription_status' => 'blocked'
        ]);
    }

    public function unblock()
    {
        $this->update([
            'is_blocked' => false,
            'block_reason' => null,
            'blocked_at' => null,
            'subscription_status' => 'active'
        ]);
    }

    public function updateSubscription($status, $startDate = null, $endDate = null, $monthlyFee = null)
    {
        $data = ['subscription_status' => $status];
        
        if ($startDate) $data['subscription_start_date'] = $startDate;
        if ($endDate) $data['subscription_end_date'] = $endDate;
        if ($monthlyFee) $data['monthly_fee'] = $monthlyFee;
        
        if ($status === 'active') {
            $data['next_payment_due'] = now()->addMonth();
        }
        
        $this->update($data);
    }

    public function getBookingLink()
    {
        // Generate a unique booking link based on company ID and slug
        $slug = Str::slug($this->company_name);
        return route('booking.page', ['company' => $this->id, 'slug' => $slug]);
    }

    public function getShareableBookingUrl()
    {
        // Generate full URL for sharing on social media
        return url('book/' . $this->id . '/' . Str::slug($this->company_name));
    }

    public function hasOnlyOneAdmin()
    {
        return $this->users()->where('role', 'admin')->count() === 1;
    }
}
