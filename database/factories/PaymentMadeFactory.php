<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PaymentMade;
use Faker\Generator as Faker;

$factory->define(PaymentMade::class, function (Faker $faker) {
    return [
        'payment_date' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'description' => $faker->sentence,
        'amount' => $faker->randomFloat(2, 100, 1000)
    ];
});
