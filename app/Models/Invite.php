<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'token',
        'role',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invite) {
            if (empty($invite->token)) {
                $invite->token = Str::random(32);
            }
        });
    }

    /**
     * Normalize phone number before saving.
     */
    public function setPhoneAttribute(?string $value): void
    {
        if ($value) {
            $this->attributes['phone'] = \App\Helpers\PhoneNormalizer::normalize($value);
        } else {
            $this->attributes['phone'] = null;
        }
    }

    /**
     * Get formatted phone number for display.
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        
        return \App\Helpers\PhoneNormalizer::formatForDisplay($this->phone);
    }
}
