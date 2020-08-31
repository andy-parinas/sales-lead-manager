<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BuildingAuthority;
use Faker\Generator as Faker;

$factory->define(BuildingAuthority::class, function (Faker $faker) {
    return [
        'approval_required' => $faker->randomElement(['yes', 'no']),
        'date_plans_sent_to_draftsman' =>  '2020-08-31',
        'date_plans_completed' =>  '2020-08-31',
        'date_plans_sent_to_authority' =>  '2020-08-31',
        'building_authority_comments' =>  $faker->paragraph,
        'date_anticipated_approval' =>  '2020-08-31',
        'date_received_from_authority' =>  '2020-08-31',
        'permit_number' => (string)$faker->randomNumber(5),
        'security_deposit_required' => $faker->randomElement(['yes', 'no']),
        'building_insurance_name' => $faker->company ,
        'building_insurance_number' => (string)$faker->randomNumber(8),
        'date_insurance_request_sent' =>  '2020-08-31',
        'lead_id' =>factory(\App\Lead::class)
    ];
});
