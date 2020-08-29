<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TradeStaffSchedule;
use Faker\Generator as Faker;

$factory->define(TradeStaffSchedule::class, function (Faker $faker) {
    return [
        'job_number' => (string)$faker->randomNumber(5),
        'anticipated_start' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'actual_start' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'anticipated_end' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'actual_end' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'trade_staff_id' => factory(\App\TradeStaff::class),
    ];
});
