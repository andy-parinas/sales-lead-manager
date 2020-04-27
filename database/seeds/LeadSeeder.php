<?php

use App\Lead;
use App\Postcode;
use App\SalesContact;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Loop Through the postcodes for the SalesContact
        Postcode::all()->each(function ($postcode){

            $franchises = $postcode->franchises;

            foreach ($franchises as $franchise){
                if(!$franchise->isParent()){
                    dump('Creating Sales Contact');
                    factory(SalesContact::class, 5)->create(['postcode' => $postcode->pcode])
                        ->each(function ($contact) use ($franchise) {

                            dump('Creating Lead');
                            $lead = factory(Lead::class)->create([
                                'sales_contact_id' => $contact->id,
                                'franchise_id' => $franchise->id
                            ]);
                            factory(\App\Appointment::class)->create(['lead_id' => $lead->id]);
                            factory(\App\JobType::class)->create(['lead_id' => $lead->id]);
                            factory(\App\Document::class)->create(['lead_id' => $lead->id]);
                        });
                }
            }
        });
    }
}
