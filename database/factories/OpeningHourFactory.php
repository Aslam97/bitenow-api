<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpeningHour>
 */
class OpeningHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $open = $this->faker->time('H:i');
        return [
            'day' => $this->faker->numberBetween(0, 6),
            'open' => $open,
            'close' => $this->faker->time('H:i', $open),
        ];
    }
}
