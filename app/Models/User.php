<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdAppointments()
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    public function assignedAppointments()
    {
        return $this->hasMany(Appointment::class, 'assigned_user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function ownedCompanies()
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function isStaff()
    {
        return $this->role !== 'admin' && $this->role !== 'super_admin' && $this->role !== 'employee';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function canManageCompany()
    {
        return $this->isAdmin();
    }

    public function canAccessSuperAdmin()
    {
        return $this->isSuperAdmin();
    }

    public function canCheckSubscriptions()
    {
        return $this->isEmployee() || $this->isSuperAdmin();
    }
}
