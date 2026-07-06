<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = ['Umum', 'Gigi', 'Anak', 'Mata', 'THT', 'Kulit'];
        $prefixes = ['A', 'B', 'C', 'D', 'E', 'F'];

        $index = fake()->numberBetween(0, count($specializations) - 1);

        return [
            'user_id' => User::factory()->doctor(),
            'specialization' => $specializations[$index],
            'queue_prefix' => $prefixes[$index],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
