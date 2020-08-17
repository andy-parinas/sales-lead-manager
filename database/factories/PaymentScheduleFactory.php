<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PaymentSchedule;
use Faker\Generator as Faker;

$factory->define(PaymentSchedule::class, function (Faker $faker) {
    return [
        'due_date' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'description' => $faker->sentence,
        'amount' => $faker->randomFloat(2, 100, 1000),
        'status' => PaymentSchedule::NOT_PAID,
        'finance_id' => factory(\App\Finance::class)
    ];
});
