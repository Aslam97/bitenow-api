<?php

namespace App\Actions\Cuisine;

use App\Http\Resources\CuisineResource;
use App\Models\Cuisine;

class CuisineList
{
    public function __invoke()
    {
        return CuisineResource::collection(Cuisine::all());
    }
}
