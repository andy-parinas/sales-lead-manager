<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\JobType;
use Faker\Generator as Faker;

$factory->define(JobType::class, function (Faker $faker) {
    return [
        'taken_by' => $faker->name,
        'date_allocated' => $faker->dateTime,
        'description' => $faker->sentence,
        'lead_id' => factory(\App\Lead::class),
        'product_id' => factory(\App\Product::class),
        'sales_staff_id' => factory(\App\SalesStaff::class)
    ];
});
