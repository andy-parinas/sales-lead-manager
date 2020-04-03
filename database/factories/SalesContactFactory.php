<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SalesContact;
use Faker\Generator as Faker;

$factory->define(SalesContact::class, function (Faker $faker) {
    return [
        'title' => $faker->randomElement(['Mr', 'Mrs', 'Ms', 'Dr']),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'contact_number' => $faker->phoneNumber,
        'postcode' => $faker->postcode
    ];
});
