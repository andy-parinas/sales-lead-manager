<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contract;
use App\Lead;
use Faker\Generator as Faker;

$factory->define(Contract::class, function (Faker $faker) {

    $price = $faker->randomFloat(2, 5000, 30000);
    $deposit = $faker->randomFloat(2, 1000, 4000);

    return [
        'contract_date' =>$faker->dateTimeThisYear($max = 'now', $timezone = null),
        'contract_number' => $faker->numerify('contract ###'),
        'contract_price' =>  $price,
        'deposit_amount' =>  $deposit,
        'date_deposit_received' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'total_variation' => 0.0,
        'total_contract' =>  $price,
        'warranty_required' => $faker->randomElement(['yes', 'no']),
        'date_warranty_sent' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'lead_id' => factory(Lead::class),
    ];
});
