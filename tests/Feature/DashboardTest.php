<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users are redirected based on role', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(); // Expecting a redirect (302)

    // Students are redirected to onboarding
    if ($user->isStudent()) {
        $response->assertRedirect(route('onboarding'));
    }
});

test('student with subscription is also redirected from dashboard to onboarding', function () {
    $user = User::factory()->create(['role' => [\App\Models\User::ROLE_STUDENT]]);
    $subscription = \App\Models\Subscription::factory()->active()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('onboarding'));
});