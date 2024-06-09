<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Cuisine;
use App\Models\OpeningHour;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $transactions = ['delivery', 'pickup', 'dine-in', 'reservations'];

        $cuisineIds = $this->seedCuisines();
        $this->seedBusinesses($cuisineIds, $transactions);

        // for measuring only
        Business::factory(1)->create([
            'name' => 'PT. Meteor Inovasi Digital',
            'coordinates' => new Point(-6.316274710641932, 106.64858981855545, Srid::WGS84->value),
        ])->each(function ($business) use ($cuisineIds, $transactions) {
            $business->cuisines()->attach($cuisineIds->random(2));
            $business->attachTags(
                collect($transactions)->random(rand(1, 4))
            );

            Review::factory(rand(1, 5))->create([
                'reviewable_id' => $business->id,
                'reviewable_type' => Business::class,
                'author_id' => User::inRandomOrder()->first()->id,
                'author_type' => User::class,
            ]);
        });
    }

    public function seedCuisines(): Collection
    {
        $colors = [
            '#D3EFDA',
            '#FFD688',
            '#FFE1DE'
        ];

        $this->getSeedData('cuisines')->each(function ($cuisine) use ($colors) {
            Cuisine::create([
                'name' => $cuisine->name,
                'image_url' => "images/cuisines/{$cuisine->image}",
                'color' => collect($colors)->random(),
            ]);
        });

        return Cuisine::all()->pluck('id');
    }

    public function seedBusinesses(Collection $cuisineIds, array $transactions): void
    {
        Business::factory(50)->create()->each(function ($business) use ($cuisineIds, $transactions) {
            $business->cuisines()->attach(
                $cuisineIds->random(rand(2, 4))
            );
            $business->attachTags(
                collect($transactions)->random(rand(1, 4))
            );

            Review::factory(rand(1, 5))->create([
                'reviewable_id' => $business->id,
                'reviewable_type' => Business::class,
                'author_id' => User::inRandomOrder()->first()->id,
                'author_type' => User::class,
            ]);

            OpeningHour::factory(rand(1, 7))->create([
                'business_id' => $business->id,
            ]);
        });
    }

    protected function getSeedData(string $filename)
    {
        return collect(json_decode(
            File::get(
                base_path("database/seeders/data/{$filename}.json")
            )
        ));
    }
}
