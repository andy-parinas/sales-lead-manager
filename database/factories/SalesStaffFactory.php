<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Franchise;
use App\SalesStaff;
use Faker\Generator as Faker;

$factory->define(SalesStaff::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'contact_number' => $faker->phoneNumber,
        'status' => $faker->randomElement([SalesStaff::ACTIVE, SalesStaff::BLOCKED]),
    ];
});
