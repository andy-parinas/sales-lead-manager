<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CustomerReview;
use Faker\Generator as Faker;

$factory->define(CustomerReview::class, function (Faker $faker) {
    return [
        'date_project_completed' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'date_warranty_received' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'home_addition_type' => $faker->word,
        'home_addition_description' => $faker->sentence,
        'service_received_rating' => $faker->randomElement(['Excellent', 'Good', 'Average', 'Poor', 'Not-Rated' ]),
        'workmanship_rating' => $faker->randomElement(['Excellent', 'Good', 'Average', 'Poor', 'Not-Rated' ]),
        'finished_product_rating' => $faker->randomElement(['Excellent', 'Good', 'Average', 'Poor', 'Not-Rated' ]),
        'design_consultant_rating' => $faker->randomElement(['Excellent', 'Good', 'Average', 'Poor', 'Not-Rated' ]),
        'comments' => $faker->sentence,
        'lead_id' => factory(\App\Lead::class)
    ];
});
