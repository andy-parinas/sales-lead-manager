<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contract;
use App\Lead;
use Faker\Generator as Faker;

$factory->define(Contract::class, function (Faker $faker) {
    return [
        'contract_date' =>$faker->dateTimeThisYear($max = 'now', $timezone = null),
        'contract_number' => $faker->numerify('contract ###'),
        'contract_price' =>  $faker->randomFloat(2, 100, 1000),
        'deposit_amount' =>  $faker->randomFloat(2, 100, 1000),
        'date_deposit_received' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'total_contract' =>  $faker->randomFloat(2, 100, 1000),
        'warranty_required' => $faker->randomElement(['yes', 'no']),
        'date_warranty_sent' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'lead_id' => factory(Lead::class),
    ];
});
