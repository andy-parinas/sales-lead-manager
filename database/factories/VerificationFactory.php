<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Verification;
use Faker\Generator as Faker;

$factory->define(Verification::class, function (Faker $faker) {
    return [
        'design_correct' => $faker->randomElement(['yes', 'no']),
        'date_design_check' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'costing_correct' => $faker->randomElement(['yes', 'no']),
        'date_costing_check' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'estimated_build_days' => $faker->randomNumber(2),
        'trades_required' => $faker->randomElement(['yes', 'no']),
        'building_supervisor' => $faker->name,
        'roof_sheet_id' => factory(\App\RoofSheet::class),
        'roof_colour_id' => factory(\App\RoofColour::class),
        'lineal_metres' => $faker->randomNumber(2),
        'franchise_authority' => $faker->company,
        'authority_date' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
    ];
});
