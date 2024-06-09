<?php

namespace App\Actions\Cuisine;

use App\Http\Resources\CuisineResource;
use App\Models\Cuisine;

class CuisineList
{
    public function __invoke()
    {
        // await 10 seconds
        sleep(1);

        return CuisineResource::collection(Cuisine::all());
    }
}
