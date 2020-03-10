<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LeadSource;
use Faker\Generator as Faker;

$factory->define(LeadSource::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true)
    ];
});
