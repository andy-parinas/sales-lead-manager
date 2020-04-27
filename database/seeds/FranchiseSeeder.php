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
        // Create A Postcode for the Franchise
        dump('Creating Postcodes');
        $postcode1 = factory(Postcode::class)->create();
        $postcode2 = factory(Postcode::class)->create();

        //Create the Parent Franchise
        dump('Creating Franchise');
        $parent1 = factory(Franchise::class)->create();
        $parent1->postcodes()->attach([$postcode1->id, $postcode2->id]);

        $child1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $child1->postcodes()->attach($postcode1->id);

        $child2 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $child2->postcodes()->attach($postcode2->id);

    }
}
