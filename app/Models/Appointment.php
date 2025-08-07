<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'service_id',
        'assigned_user_id',
        'created_by',
        'location_id',
        'appointment_date',
        'end_time',
        'status',
        'price',
        'notes',
        'reminder_settings',
        'reminder_sent_at',
        'calendar_event_id',
        'synced_at',
        'sync_metadata'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'end_time' => 'datetime',
        'reminder_settings' => 'array',
        'reminder_sent_at' => 'datetime',
        'synced_at' => 'datetime',
        'sync_metadata' => 'array',
        'price' => 'decimal:2'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', Carbon::today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', Carbon::now());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getDurationAttribute()
    {
        return $this->appointment_date->diffInMinutes($this->end_time);
    }

    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date > Carbon::now();
    }
}
