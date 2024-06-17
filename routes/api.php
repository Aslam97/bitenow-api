<?php

use App\Actions\Business;
use App\Actions\ReverseGeocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/cuisines', \App\Actions\Cuisine\CuisineList::class);
Route::get('/businesses', Business\BusinessList::class);
Route::post('/reverse-geo', ReverseGeocode::class);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
