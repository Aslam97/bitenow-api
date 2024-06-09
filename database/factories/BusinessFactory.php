<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = $this->faker->unique()->slug;

        return [
            'name' => $this->faker->company,
            'slug' => $slug,
            'image_url' => $this->faker->imageUrl(),
            'url' => url('/businesses/' . $slug),
            'phone' => $this->faker->e164PhoneNumber(),
            'phone_country_code' => $this->faker->countryCode,
            'price' => $this->faker->randomElement([1, 2, 3, 4, 5]),

            // Address
            'address1' => $this->faker->address,
            'address2' => $this->faker->address,
            'city' => $this->faker->city,
            'zip_code' => $this->faker->postcode,
            'country' => $this->faker->countryCode,
            'state' => $this->faker->state,
            'coordinates' => new Point($this->faker->latitude, $this->faker->longitude, Srid::WGS84->value),
        ];
    }
}
