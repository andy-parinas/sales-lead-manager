<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DesignAssessor;
use Faker\Generator as Faker;

$factory->define(DesignAssessor::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' =>$faker->lastName,
        'email' =>$faker->email,
        'contact_number' => $faker->phoneNumber
    ];
});
