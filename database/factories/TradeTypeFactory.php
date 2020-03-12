<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TradeType;
use Faker\Generator as Faker;

$factory->define(TradeType::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true)
    ];
});
