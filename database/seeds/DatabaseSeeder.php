<?php

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
            PostcodeSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            LeadSourceSeeder::class,
            LeadSeeder::class
        ]);

    }
}
