<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PricingTier>
 */
class PricingTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true) . ' Plan',
            'description' => $this->faker->sentence(),
            'class_count' => $this->faker->numberBetween(4, 20),
            'price' => $this->faker->randomFloat(2, 50, 300),
            'comparative_price' => $this->faker->optional(0.7)->randomFloat(2, 60, 350),
            'billing_period' => $this->faker->randomElement(['monthly', 'quarterly', 'yearly']),
            'frequency_type' => $this->faker->randomElement(['recurring', 'one-time']),
            'class_cap' => $this->faker->numberBetween(1, 5),
            'is_active' => true,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
