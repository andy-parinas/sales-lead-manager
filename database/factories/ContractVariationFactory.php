<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contract;
use App\ContractVariation;
use Faker\Generator as Faker;

$factory->define(ContractVariation::class, function (Faker $faker) {
    return [
        'variation_date' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'description' => $faker->sentence,
        'amount' => $faker->randomFloat(2, 10, 90),
        'contract_id' => factory(Contract::class)
    ];
});
