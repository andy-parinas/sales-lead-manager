<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Branch;
use App\Lead;
use Faker\Generator as Faker;

$factory->define(Lead::class, function (Faker $faker) {
    return [
        'number' => strval($faker->randomNumber(6)),
        'branch_id' => factory(Branch::class)
    ];
});
