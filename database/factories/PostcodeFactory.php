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
        'presort_indicator' => '0' . (string) $faker->random_int(10, 99),
        'parcel_zone' => $faker->randomElements(['N2', 'Q1', 'V1', 'S2']),
        'bsp_number' => '0'. (string) $faker->random_int(10, 99),
        'category' => $faker->randomElements(['DELIVERY AREA', 'POST OFFICE BOX']),
        'comments' => $faker->sentence

    ];
});
