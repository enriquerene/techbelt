<?php

namespace Database\Factories;

use App\Models\GymClass;
use App\Models\PricingTier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => \App\Models\User::ROLE_STUDENT]),
            'pricing_tier_id' => PricingTier::factory(),
            'amount' => $this->faker->optional()->randomFloat(2, 50, 200),
            'is_custom_price' => $this->faker->boolean(30), // 30% chance of custom price
            'enrolled_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'next_billing_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['active', 'cancelled', 'overdue']),
            'notes' => $this->faker->optional()->sentence(),
            'cancellation_reason' => null,
            'cancelled_at' => null,
            'created_by' => User::factory()->create(['role' => \App\Models\User::ROLE_STAFF]),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'cancellation_reason' => null,
            'cancelled_at' => null,
        ]);
    }

    public function cancelled(string $reason = 'Personal reasons'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'next_billing_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }
}
