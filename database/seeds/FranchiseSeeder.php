<?php

use App\Franchise;
use App\Postcode;
use Illuminate\Database\Seeder;

class FranchiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $parent = factory(Franchise::class)->create();

        $franchises = factory(Franchise::class, 5)->create(['parent_id' => $parent->id]);


        foreach ($franchises as $franchise){

            $postcodes = factory(Postcode::class, 2)->create();

            foreach ($postcodes as $postcode){

                $parent->postcodes()->attach($postcode->id);
                $franchise->postcodes()->attach($postcode->id);

            }
        }

    }
}
