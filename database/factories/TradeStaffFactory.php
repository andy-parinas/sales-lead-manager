<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TradeStaff;
use Faker\Generator as Faker;

$factory->define(TradeStaff::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'contact_number' => $faker->phoneNumber,
        'trade_type_id' => factory(\App\TradeType::class),
        'franchise_id' => factory(\App\Franchise::class)
    ];
});
