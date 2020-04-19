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
        'email2' => $faker->email,
        'contact_number' => $faker->phoneNumber,
        'street1' => $faker->streetAddress,
        'suburb' => $faker->city,
        'state' => $faker->state,
        'postcode' => $faker->postcode,
        'customer_type' => $faker->randomElement([SalesContact::COMMERCIAL, SalesContact::RESIDENTIAL])
    ];
});
