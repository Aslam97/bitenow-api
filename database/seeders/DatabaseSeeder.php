<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Point;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $categories = ['Tapas/Small Plates', 'Cocktail Bars', 'American (New)', 'Breakfast & Brunch', 'Seafood', 'Steakhouses', 'Italian', 'Mexican', 'Japanese', 'Chinese'];

        // for measuring only
        Business::factory(1)->create([
            'name' => 'PT. Meteor Inovasi Digital',
            'coordinates' => new Point(-6.316274710641932, 106.64858981855545, Srid::WGS84->value),
        ])->each(function ($business) use ($categories) {
            $business->attachTags(
                collect($categories)->random(rand(2, 4))
            );

            Review::factory(rand(1, 5))->create([
                'reviewable_id' => $business->id,
                'reviewable_type' => Business::class,
                'author_id' => User::inRandomOrder()->first()->id,
                'author_type' => User::class,
            ]);
        });

        Business::factory(10)->create()->each(function ($business) use ($categories) {
            $business->attachTags(
                collect($categories)->random(rand(2, 4))
            );

            Review::factory(rand(1, 5))->create([
                'reviewable_id' => $business->id,
                'reviewable_type' => Business::class,
                'author_id' => User::inRandomOrder()->first()->id,
                'author_type' => User::class,
            ]);
        });
    }
}
