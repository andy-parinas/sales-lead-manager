<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

//use \App\Branch;
use Faker\Generator as Faker;

$factory->define(\App\Branch::class, function (Faker $faker) {
    return [
        'number' => strval($faker->randomNumber(4)),
        'name' => $faker->words(2, true),
        'description' => $faker->sentence,
    ];
});
