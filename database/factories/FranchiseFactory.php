<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

//use \App\Branch;
use Faker\Generator as Faker;

$factory->define(\App\Franchise::class, function (Faker $faker) {
    return [
        'franchise_number' => strval($faker->randomNumber(4)),
        'name' => $faker->words(2, true),
        'description' => $faker->sentence,
    ];
});
