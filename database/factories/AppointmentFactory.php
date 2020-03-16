<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Appointment;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    return [
        'appointment_date' => $faker->dateTime,
        'appointment_notes' => $faker->paragraph,
        'quoted_price' => $faker->randomNumber(),
        'outcome' => $faker->randomElement([
            'pending', 'follow-up', 'lost', 'success', 'deferred', 'cancelled', 'did not proceed'
        ]),
        'comments' => $faker->paragraph,
        'lead_id' => factory(\App\Lead::class)
    ];

});
