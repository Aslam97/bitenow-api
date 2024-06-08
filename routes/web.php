<?php

use App\Actions\Business\BusinessList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return BusinessList::run();

    return ['Yelp' => app()->version()];
});

require __DIR__.'/auth.php';
