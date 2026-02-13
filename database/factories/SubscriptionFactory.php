<?php

namespace Database\Factories;

use App\Models\PricingTier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $endsAt = $this->faker->dateTimeBetween($startsAt, '+1 year');

        return [
            'user_id' => User::factory()->create(['role' => \App\Models\User::ROLE_STUDENT]),
            'pricing_tier_id' => PricingTier::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $this->faker->randomElement(['active', 'expired', 'cancelled']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'starts_at' => now()->subYear(),
            'ends_at' => now()->subMonth(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addWeek(),
        ]);
    }
}
