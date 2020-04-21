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
            //Create the Franchise
            $parent = factory(Franchise::class)->create();

            //Children Franchise
            $c1 = factory(Franchise::class)->create(['parent_id' => $parent->id]);
            $c2 = factory(Franchise::class)->create(['parent_id' => $parent->id]);

            //Create a Franchise Admin to be assigned to the Parent Franchise
            $fa = factory(User::class)->create([
                'username' => 'franchiseAdmin',
                'user_type' => User::FRANCHISE_ADMIN
            ]);
            $fa->franchises()->attach($parent->id);

            //Crate the staff users for each children
            $su = factory(User::class)->create([
                'username' => 'stafUser1',
                'user_type' => User::STAFF_USER
            ]);

            //Create Postcode
            $postcode = factory(Postcode::class)->create();

            //Attached the postcode to Parent and C1
            $parent->postcodes()->attach($postcode->id);
            $c1->postcodes()->attach($postcode->id);

            //Create SalesContact using the postcode above
            $salesContact = factory(SalesContact::class)->create(['postcode' => $postcode->pcode]);

            //Create Lead using the SalesContact and under the c1 franchise
            $lead = factory(Lead::class)->create([
                'sales_contact_id' => $salesContact->id,
                'franchise_id' => $c1->id,
            ]);


    }
}
