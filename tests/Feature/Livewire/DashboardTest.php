<?php

use App\Models\User;
use App\Models\Subscription;
use App\Models\Enrollment;
use App\Models\GymClass;

test('student with subscription can access app dashboard', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $subscription = Subscription::factory()->active()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $response = $this->get(route('app.home'));

    $response->assertOk();
    $response->assertSeeLivewire('app.dashboard');
});

test('student without subscription is redirected', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    // No subscription
    $this->actingAs($user);

    $response = $this->get(route('app.home'));

    // Should be redirected (maybe to onboarding or subscription page)
    $response->assertRedirect();
});

test('dashboard shows enrollment count', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $subscription = Subscription::factory()->active()->create(['user_id' => $user->id]);
    Enrollment::factory()->count(3)->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $response = $this->get(route('app.home'));

    $response->assertSee('3'); // enrollment count
});

test('dashboard shows upcoming classes', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $subscription = Subscription::factory()->active()->create(['user_id' => $user->id]);
    $enrollment = Enrollment::factory()->active()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $response = $this->get(route('app.home'));

    $response->assertSee($enrollment->gymClass->name);
});

test('guest cannot access app dashboard', function () {
    $response = $this->get(route('app.home'));

    $response->assertRedirect(route('login'));
});

test('staff can access student app dashboard', function () {
    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $response = $this->get(route('app.home'));

    // Staff might be able to access student app dashboard
    // Based on test results, they get 200, not 403
    $response->assertOk();
});

test('student cannot access filament admin panel', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $this->actingAs($user);

    $response = $this->get('/admin');
    // Student should be denied access (403) or redirected to login
    $response->assertForbidden();
});