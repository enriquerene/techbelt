<?php

use App\Models\Enrollment;
use App\Models\User;
use App\Models\GymClass;
use App\Models\PricingTier;

test('enrollment belongs to user', function () {
    $enrollment = Enrollment::factory()->create();

    expect($enrollment->user)->toBeInstanceOf(User::class);
});

test('enrollment has many classes relationship', function () {
    $enrollment = Enrollment::factory()->create();
    $gymClass = GymClass::factory()->create();
    
    // Attach a class
    $enrollment->classes()->attach($gymClass->id);
    $enrollment->refresh();

    expect($enrollment->classes)->toHaveCount(1)
        ->and($enrollment->classes->first())->toBeInstanceOf(GymClass::class);
});

test('enrollment belongs to pricing tier', function () {
    $enrollment = Enrollment::factory()->create();

    expect($enrollment->pricingTier)->toBeInstanceOf(PricingTier::class);
});

test('enrollment has amount cast to decimal', function () {
    $enrollment = Enrollment::factory()->create(['amount' => 99.99]);

    // Decimal cast returns a string
    expect($enrollment->amount)->toBe('99.99');
    expect($enrollment->getCasts()['amount'])->toBe('decimal:2');
});

test('enrollment status scopes', function () {
    $active = Enrollment::factory()->active()->create();
    $cancelled = Enrollment::factory()->cancelled()->create();
    $overdue = Enrollment::factory()->overdue()->create();

    expect(Enrollment::active()->count())->toBe(1)
        ->and(Enrollment::active()->first()->id)->toBe($active->id);

    expect(Enrollment::cancelled()->count())->toBe(1)
        ->and(Enrollment::cancelled()->first()->id)->toBe($cancelled->id);

    expect(Enrollment::overdue()->count())->toBe(1)
        ->and(Enrollment::overdue()->first()->id)->toBe($overdue->id);
});

test('enrollment status helper methods', function () {
    $active = Enrollment::factory()->active()->create();
    $cancelled = Enrollment::factory()->cancelled()->create();
    $overdue = Enrollment::factory()->overdue()->create();

    expect($active->isActive())->toBeTrue()
        ->and($active->isCancelled())->toBeFalse()
        ->and($active->isOverdue())->toBeFalse();

    expect($cancelled->isActive())->toBeFalse()
        ->and($cancelled->isCancelled())->toBeTrue()
        ->and($cancelled->isOverdue())->toBeFalse();

    expect($overdue->isActive())->toBeFalse()
        ->and($overdue->isCancelled())->toBeFalse()
        ->and($overdue->isOverdue())->toBeTrue();
});

test('enrollment fillable attributes', function () {
    $enrollment = new Enrollment();

    expect($enrollment->getFillable())->toBe([
        'user_id',
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
    ]);
});

test('enrollment datetime casts', function () {
    $enrollment = new Enrollment();

    expect($enrollment->getCasts())->toHaveKeys(['enrolled_at', 'next_billing_date', 'cancelled_at']);
    expect($enrollment->getCasts()['enrolled_at'])->toBe('datetime');
    expect($enrollment->getCasts()['next_billing_date'])->toBe('datetime');
    expect($enrollment->getCasts()['cancelled_at'])->toBe('datetime');
});