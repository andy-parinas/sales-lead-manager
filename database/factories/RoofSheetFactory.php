<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\RoofSheet;
use Faker\Generator as Faker;

$factory->define(RoofSheet::class, function (Faker $faker) {
    return [
        'name' => $faker->word
    ];
});
