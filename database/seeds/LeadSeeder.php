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
        $designAdvisors = factory(\App\DesignAssessor::class, 20)->create();
        $leadSourceIds = LeadSource::all()->pluck('id')->toArray();
        $productIds = \App\Product::all()->pluck('id')->toArray();
        $designAdvisorIds = $designAdvisors->pluck('id')->toArray();

        //Loop Through the postcodes for the SalesContact
        Postcode::all()->each(function ($postcode) use ($leadSourceIds, $productIds, $designAdvisorIds) {

            $franchises = $postcode->franchises;

            foreach ($franchises as $franchise){
                if(!$franchise->isParent()){
                    dump('Creating Sales Contact');
                    factory(SalesContact::class, 60)->create(['postcode' => $postcode->pcode])
                        ->each(function ($contact) use ($franchise, $leadSourceIds, $productIds, $designAdvisorIds) {

                            dump('Creating Lead');
                            $leadSourceKey = array_rand($leadSourceIds);
                            $productKey = array_rand($productIds);
                            $designAdvisorKey = array_rand($designAdvisorIds);

                            $lead = factory(Lead::class)->create([
                                'sales_contact_id' => $contact->id,
                                'franchise_id' => $franchise->id,
                                'lead_source_id' => $leadSourceIds[$leadSourceKey]
                            ]);
                            factory(\App\Appointment::class)->create(['lead_id' => $lead->id]);
                            factory(\App\JobType::class)->create([
                                'lead_id' => $lead->id,
                                'product_id' => $productIds[$productKey],
                                'design_assessor_id' => $designAdvisorIds[$designAdvisorKey]
                            ]);
                            factory(\App\Document::class)->create(['lead_id' => $lead->id]);
                        });
                }
            }

        });
    }
}
