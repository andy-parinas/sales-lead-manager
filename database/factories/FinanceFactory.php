<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Finance;
use App\Lead;
use Faker\Generator as Faker;

$factory->define(Finance::class, function (Faker $faker) {

    $contract_price = $faker->randomFloat(2, 100, 1000);
    $project_price = $contract_price / 1.1;
    $gst = $project_price * 0.10;
    $deposit = $deposit = $faker->randomFloat(2, 10, 90);
    $balance = $contract_price - $deposit;


    return [
        'project_price' => $project_price,
        'gst' => $gst,
        'contract_price' => $contract_price,
        'total_contract' => $contract_price,
        'deposit' => $deposit,
        'balance' => $balance,
        'total_payment_made' => 0,
        'lead_id' => factory(Lead::class)
    ];
});
