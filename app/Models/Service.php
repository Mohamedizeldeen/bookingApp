<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'availability'
    ];

    protected $casts = [
        'availability' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Accessors
    public function getDurationAttribute()
    {
        return $this->duration_minutes;
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
