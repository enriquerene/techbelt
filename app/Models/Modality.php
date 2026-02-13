<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function classes()
    {
        return $this->hasMany(GymClass::class);
    }

    public function enrollments()
    {
        return $this->hasManyThrough(
            Enrollment::class,
            GymClass::class,
            'modality_id', // Foreign key on GymClass table
            'class_id',    // Foreign key on Enrollment table
            'id',          // Local key on Modality table
            'id'           // Local key on GymClass table
        );
    }

    public function getStudentsCountAttribute()
    {
        if (!array_key_exists('students_count', $this->attributes)) {
            // If not eager-loaded, compute via subquery
            $this->attributes['students_count'] = $this->enrollments()
                ->distinct('user_id')
                ->count('user_id');
        }
        return $this->attributes['students_count'];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($modality) {
            if (empty($modality->slug)) {
                $modality->slug = \Illuminate\Support\Str::slug($modality->name);
            }
        });

        static::updating(function ($modality) {
            if ($modality->isDirty('name') && empty($modality->slug)) {
                $modality->slug = \Illuminate\Support\Str::slug($modality->name);
            }
        });
    }
}
