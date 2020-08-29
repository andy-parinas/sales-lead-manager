<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BuildLog;
use Faker\Generator as Faker;

$factory->define(BuildLog::class, function (Faker $faker) {

    $timeSpent =  $faker->randomFloat(2, 1, 8);
    $hourlyRate = $faker->randomFloat(2, 20, 200);

    return [
        'work_date' =>  $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'time_spent' => $timeSpent,
        'hourly_rate' => $hourlyRate,
        'total_cost' => $timeSpent * $hourlyRate,
        'construction_id' => factory(\App\Construction::class),
        'trade_staff_id' => factory(\App\TradeStaff::class)
    ];
});

