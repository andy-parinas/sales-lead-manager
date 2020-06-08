<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Document;
use App\Lead;
use Faker\Generator as Faker;

$factory->define(Document::class, function (Faker $faker) {

    $fileType = $faker->randomElement(['docx', 'xlsx', 'pptx', 'pdf', 'txt']);
    $path = "https://filerepository.com/" . $faker->md5 . "." . $fileType;

    return [
        'title' => $faker->sentence,
        'path'  => $path,
        'type' => $fileType,
        'lead_id' => factory(Lead::class)
    ];
});
