<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence,
            'helpful' => $this->faker->numberBetween(0, 100),
            'thanks' => $this->faker->numberBetween(0, 100),
            'love_this' => $this->faker->boolean,
            'oh_no' => $this->faker->boolean,
        ];
    }
}
