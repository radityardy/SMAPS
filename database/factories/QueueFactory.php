<?php

namespace Database\Factories;

use App\Enums\QueueStatus;
use App\Models\Doctor;
use App\Models\Queue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Queue>
 */
class QueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'patient_name' => fake()->name(),
            'patient_phone' => fake()->phoneNumber(),
            'complaint' => fake()->sentence(),
            'queue_number' => 'A-'.fake()->unique()->numberBetween(1, 999),
            'status' => QueueStatus::Waiting,
            'queue_date' => now()->toDateString(),
        ];
    }

    public function called(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueStatus::Called,
            'called_at' => now(),
        ]);
    }

    public function serving(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueStatus::Serving,
            'called_at' => now()->subMinutes(5),
            'served_at' => now(),
        ]);
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueStatus::Done,
            'called_at' => now()->subMinutes(30),
            'served_at' => now()->subMinutes(25),
            'completed_at' => now(),
        ]);
    }
}
