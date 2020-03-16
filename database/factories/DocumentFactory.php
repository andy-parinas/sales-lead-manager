<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Document;
use App\Lead;
use Faker\Generator as Faker;

$factory->define(Document::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'path'  => $faker->words(3, true),
        'type' => $faker->word,
        'lead_id' => factory(Lead::class)
    ];
});
