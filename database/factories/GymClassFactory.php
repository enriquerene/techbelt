<?php

namespace Database\Factories;

use App\Models\Modality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GymClass>
 */
class GymClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'modality_id' => Modality::factory(),
            'instructor_id' => User::factory()->create(['role' => \App\Models\User::ROLE_STAFF]),
            'name' => $this->faker->words(3, true),
            'capacity' => $this->faker->numberBetween(5, 30),
            'schedule' => json_encode([
                'monday' => ['start' => '09:00', 'end' => '10:00'],
                'wednesday' => ['start' => '09:00', 'end' => '10:00'],
                'friday' => ['start' => '09:00', 'end' => '10:00'],
            ]),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
