<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'description',
        'working_hours',
        'amenities',
        'is_active',
        'is_main_location',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'amenities' => 'array',
        'is_active' => 'boolean',
        'is_main_location' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the company that owns the location
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get appointments for this location
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the full address as a string
     */
    public function getFullAddressAttribute(): string
    {
        return trim("{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}");
    }

    /**
     * Check if location is open at a given time
     */
    public function isOpenAt(\DateTime $dateTime): bool
    {
        if (!$this->working_hours) {
            return false;
        }

        $dayOfWeek = strtolower($dateTime->format('l'));
        $time = $dateTime->format('H:i');

        if (!isset($this->working_hours[$dayOfWeek])) {
            return false;
        }

        $hours = $this->working_hours[$dayOfWeek];
        
        if (!$hours['is_open']) {
            return false;
        }

        return $time >= $hours['open'] && $time <= $hours['close'];
    }

    /**
     * Scope to get active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get main location
     */
    public function scopeMain($query)
    {
        return $query->where('is_main_location', true);
    }
}
