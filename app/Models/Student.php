<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Student extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('student', function (Builder $builder) {
            $builder->whereJsonContains('role', self::ROLE_STUDENT);
        });
    }

    /**
     * Get the subscriptions of this student.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    /**
     * Get the enrollments of this student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    /**
     * Get the graduations of this student.
     */
    public function graduations()
    {
        return $this->hasMany(Graduation::class, 'user_id');
    }

    /**
     * Get the attendances of this student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}