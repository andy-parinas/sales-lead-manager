<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Franchise;
use App\Lead;
use App\LeadSource;
use App\SalesContact;
use Faker\Generator as Faker;

$factory->define(Lead::class, function (Faker $faker) {
    return [
        'lead_number' => strval($faker->randomNumber(6)),
        'franchise_id' => factory(Franchise::class),
        'sales_contact_id' =>factory(SalesContact::class),
        'lead_source_id' =>factory(LeadSource::class),
        'lead_date' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'postcode_status' => $faker->randomElement([Lead::INSIDE_OF_FRANCHISE, Lead::OUTSIDE_OF_FRANCHISE])
    ];
});
