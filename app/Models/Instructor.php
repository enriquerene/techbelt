<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Instructor extends User
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
        static::addGlobalScope('instructor', function (Builder $builder) {
            $builder->where(function ($query) {
                $query->whereJsonContains('role', self::ROLE_STAFF)
                      ->orWhereJsonContains('role', self::ROLE_ADMIN);
            });
        });
    }

    /**
     * Get the classes taught by this instructor.
     */
    public function classes()
    {
        return $this->hasMany(GymClass::class, 'instructor_id');
    }
}