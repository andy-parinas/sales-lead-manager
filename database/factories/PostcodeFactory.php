<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Postcode;
use Faker\Generator as Faker;

$factory->define(Postcode::class, function (Faker $faker) {

    $city = $faker->city;

    return [
        'pcode' => $faker->postcode,
        'locality' => $city,
        'state' =>$faker->state,
        'delivery_office' => strtoupper($city) . ' DELIVERY',
        'presort_indicator' => strval($faker->numberBetween(10, 99)), 
        'parcel_zone' => $faker->randomElement(['N2', 'Q1', 'V1', 'S2']),
        'bsp_number' => strval($faker->numberBetween(10, 99)),
        'bsp_name' => $city,
        'category' => $faker->randomElement(['DELIVERY AREA', 'POST OFFICE BOX']),
        'comments' => $faker->sentence

    ];
});
