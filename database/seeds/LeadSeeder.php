<?php

use App\Lead;
use App\LeadSource;
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
        $leadSourceIds = LeadSource::all()->pluck('id')->toArray();

        //Loop Through the postcodes for the SalesContact
        Postcode::all()->each(function ($postcode) use ($leadSourceIds) {

            $franchises = $postcode->franchises;

            foreach ($franchises as $franchise){
                if(!$franchise->isParent()){
                    dump('Creating Sales Contact');
                    factory(SalesContact::class, 60)->create(['postcode' => $postcode->pcode])
                        ->each(function ($contact) use ($franchise, $leadSourceIds) {

                            dump('Creating Lead');
                            $leadSourceKey = array_rand($leadSourceIds);

                            $lead = factory(Lead::class)->create([
                                'sales_contact_id' => $contact->id,
                                'franchise_id' => $franchise->id,
                                'lead_source_id' => $leadSourceIds[$leadSourceKey]
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
