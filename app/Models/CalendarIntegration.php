<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CalendarIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'provider',
        'calendar_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'sync_settings',
        'last_sync_at',
        'is_active',
        'two_way_sync',
        'sync_direction',
    ];

    protected $casts = [
        'sync_settings' => 'array',
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
        'two_way_sync' => 'boolean',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the user that owns the calendar integration
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the calendar integration
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if the access token is expired
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }

    /**
     * Check if sync is needed
     */
    public function needsSync(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->last_sync_at) {
            return true;
        }

        // Get sync frequency from settings (default 15 minutes)
        $syncFrequency = $this->sync_settings['frequency'] ?? 15;
        
        return $this->last_sync_at->diffInMinutes(now()) >= $syncFrequency;
    }

    /**
     * Check if token needs refresh
     */
    public function needsTokenRefresh(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        // Refresh token if it expires within 5 minutes
        return $this->token_expires_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Check if integration can sync
     */
    public function canSync(): bool
    {
        return $this->is_active && 
               !empty($this->access_token) && 
               !$this->isTokenExpired();
    }

    /**
     * Get sync status
     */
    public function getSyncStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->isTokenExpired()) {
            return 'token_expired';
        }

        if ($this->needsSync()) {
            return 'pending';
        }

        return 'synced';
    }

    /**
     * Get provider display name
     */
    public function getProviderDisplayNameAttribute(): string
    {
        return match($this->provider) {
            'google' => 'Google Calendar',
            'outlook' => 'Microsoft Outlook',
            'apple' => 'Apple Calendar',
            'caldav' => 'CalDAV',
            default => ucfirst($this->provider),
        };
    }

    /**
     * Scope to get active integrations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get integrations that need sync
     */
    public function scopeNeedsSync($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('last_sync_at')
                          ->orWhere('last_sync_at', '<=', now()->subMinutes(15));
                    });
    }
}
