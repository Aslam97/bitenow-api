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
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->unique()->slug,
            'image_url' => $this->faker->imageUrl(),
            'url' => $this->faker->url,
            'phone' => $this->faker->e164PhoneNumber(),
            'phone_country_code' => $this->faker->countryCode,

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
