<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'pricing_tier_id',
        'amount',
        'is_custom_price',
        'enrolled_at',
        'next_billing_date',
        'status',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'created_by',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'is_custom_price' => 'boolean',
    ];

    protected $appends = [
        'final_price',
        'is_custom_price',
    ];

    public function user()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function gymClass()
    {
        return $this->belongsTo(GymClass::class, 'class_id');
    }

    public function pricingTier()
    {
        return $this->belongsTo(PricingTier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // New relationships
    public function classes()
    {
        return $this->belongsToMany(GymClass::class, 'enrollment_class', 'enrollment_id', 'gym_class_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    // Price calculation methods
    public function getFinalPriceAttribute()
    {
        if ($this->amount !== null && $this->is_custom_price) {
            return $this->amount;
        }

        return $this->pricingTier?->price ?? 0;
    }

    public function getIsCustomPriceAttribute()
    {
        return (bool) ($this->attributes['is_custom_price'] ?? false);
    }

    public function setIsCustomPriceAttribute($value)
    {
        $this->attributes['is_custom_price'] = (bool) $value;
        
        // If custom price is false and amount is null, we should keep it null
        // If custom price is true but amount is null, we should set it to pricing tier price
        if ($value && $this->amount === null) {
            $this->amount = $this->pricingTier?->price ?? 0;
        }
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value;
        
        // If amount is set (not null), automatically mark as custom price
        if ($value !== null) {
            $this->attributes['is_custom_price'] = true;
        }
    }

    // Class management methods
    public function canEnrollInMoreClasses(): bool
    {
        if (!$this->pricingTier) {
            return false;
        }

        $currentCount = $this->classes()->count();
        return $currentCount < $this->pricingTier->class_count;
    }

    public function getRemainingClassSlots(): int
    {
        if (!$this->pricingTier) {
            return 0;
        }

        $currentCount = $this->classes()->count();
        return max(0, $this->pricingTier->class_count - $currentCount);
    }

    // Payment helper methods
    public function hasCompletedPayment(): bool
    {
        return $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->exists();
    }

    public function getLatestPayment()
    {
        return $this->payments()
            ->latest('paid_at')
            ->first();
    }
}
