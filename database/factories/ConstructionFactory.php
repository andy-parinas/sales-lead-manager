<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Construction;
use Faker\Generator as Faker;

$factory->define(Construction::class, function (Faker $faker) {
    return [
        'site_address' => $faker->streetAddress,
        'postcode_id' => factory(\App\Postcode::class),
        'trade_staff_id' => factory(\App\TradeStaff::class)
    ];
});
