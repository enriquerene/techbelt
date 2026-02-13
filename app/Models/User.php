<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    public const ROLE_STUDENT = 'student';
    public const ROLE_STAFF = 'staff';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'role' => 'array',
        ];
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->role ?? [], true);
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    /**
     * Check if the user is staff.
     */
    public function isStaff(): bool
    {
        return $this->hasRole(self::ROLE_STAFF);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // Relationships for Student App
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function graduations()
    {
        return $this->hasMany(Graduation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Instructor relationships
    public function classes()
    {
        return $this->hasMany(GymClass::class, 'instructor_id');
    }
}
