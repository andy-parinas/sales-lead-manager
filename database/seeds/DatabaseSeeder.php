<?php

use App\Branch;
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
        //First Level Branch
        $main = factory(Branch::class)->create(['number' => '000', 'name' => 'Main Branch', 'description' => 'Head Office Branch']);

        //Second Level Branch
        $branches1 = factory(Branch::class, 3)->create(['parent_id' => $main->id]);

        foreach ($branches1 as $branch){
            factory(Branch::class, 3)->create(['parent_id' => $branch->id]);
        }

    }
}
