<?php

use App\Branch;
use App\Franchise;
use App\Lead;
use App\Postcode;
use App\SalesContact;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
           FranchiseSeeder::class,
           UserSeeder::class,
           LeadSourceSeeder::class,
           LeadSeeder::class
        ]);

    }
}
