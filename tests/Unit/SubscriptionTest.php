<?php

use App\Models\Subscription;
use App\Models\User;
use App\Models\PricingTier;

test('subscription belongs to user', function () {
    $subscription = Subscription::factory()->create();

    expect($subscription->user)->toBeInstanceOf(User::class);
});

test('subscription belongs to pricing tier', function () {
    $subscription = Subscription::factory()->create();

    expect($subscription->pricingTier)->toBeInstanceOf(PricingTier::class);
});

test('subscription has datetime casts', function () {
    $subscription = new Subscription();

    expect($subscription->getCasts())->toHaveKeys(['starts_at', 'ends_at']);
    expect($subscription->getCasts()['starts_at'])->toBe('datetime');
    expect($subscription->getCasts()['ends_at'])->toBe('datetime');
});

test('subscription fillable attributes', function () {
    $subscription = new Subscription();

    expect($subscription->getFillable())->toBe([
        'user_id',
        'pricing_tier_id',
        'starts_at',
        'ends_at',
        'status',
    ]);
});

test('subscription factory states work', function () {
    $active = Subscription::factory()->active()->create();
    $expired = Subscription::factory()->expired()->create();
    $cancelled = Subscription::factory()->cancelled()->create();

    expect($active->status)->toBe('active')
        ->and($active->starts_at->isPast())->toBeTrue()
        ->and($active->ends_at->isFuture())->toBeTrue();

    expect($expired->status)->toBe('expired')
        ->and($expired->ends_at->isPast())->toBeTrue();

    expect($cancelled->status)->toBe('cancelled');
});

test('subscription status is stored as string', function () {
    $subscription = Subscription::factory()->create(['status' => 'active']);

    expect($subscription->status)->toBe('active');
});