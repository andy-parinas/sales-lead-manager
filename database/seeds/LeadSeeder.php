<?php

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\Product;
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


        $franchises = Franchise::where('parent_id', '<>', null)->get();

        $franchises->each(function ($franchise){

            print "Creating Sales Staff \n";
            $salesStaff = factory(\App\SalesStaff::class)->create(['franchise_id' => $franchise->id]);

            print "Creating 5 Sales Contacts \n";
            $postcode = $franchise->postcodes->random();
            $salesContacts = factory(SalesContact::class, 5)->create(['postcode_id' => $postcode->id]);


            foreach ($salesContacts as $salesContact){

                $leadSource = LeadSource::all()->random();

                print "Creating Lead \n";
                $lead = factory(Lead::class)->create([
                    'sales_contact_id' => $salesContact->id,
                    'franchise_id' => $franchise->id,
                    'lead_source_id' => $leadSource->id,
                    'postcode_status' => Lead::INSIDE_OF_FRANCHISE
                ]);

                $product = Product::all()->random();

                print "Creating Job Type \n";
                factory(JobType::class)->create([
                    'lead_id' => $lead->id,
                    'sales_staff_id' => $salesStaff->id,
                    'product_id' => $product->id
                ]);

                print "Creating Appointment \n";
                factory(Appointment::class)->create(['lead_id' => $lead->id]);

                print "Creating Contract \n";
                $contract = factory(\App\Contract::class)->create([
                    'lead_id' => $lead->id,
                ]);

                print "Creating Finance \n";
                factory(\App\Finance::class)->create([
                    'project_price' => $contract->contract_price / 1.1,
                    'gst' => 0.10,
                    'contract_price' => $contract->contract_price,
                    'total_contract' => $contract->total_contract,
                    'deposit' => $contract->deposit_amount,
                    'balance' => $contract->total_contract - $contract->deposit_amount,
                    'total_payment_made' => 0,
                    'lead_id' => $lead->id
                ]);

            }

        });



//        $designAdvisors = factory(\App\DesignAssessor::class, 20)->create();
//        $leadSourceIds = LeadSource::all()->pluck('id')->toArray();
//        $productIds = \App\Product::all()->pluck('id')->toArray();
//        $designAdvisorIds = $designAdvisors->pluck('id')->toArray();
//
//        //Loop Through the postcodes for the SalesContact
//        Postcode::all()->each(function ($postcode) use ($leadSourceIds, $productIds, $designAdvisorIds) {
//
//            $franchises = $postcode->franchises;
//
//            foreach ($franchises as $franchise){
//                if(!$franchise->isParent()){
//                    dump('Creating Sales Contact');
//                    factory(SalesContact::class, 60)->create(['postcode' => $postcode->pcode])
//                        ->each(function ($contact) use ($franchise, $leadSourceIds, $productIds, $designAdvisorIds) {
//
//                            dump('Creating Lead');
//                            $leadSourceKey = array_rand($leadSourceIds);
//                            $productKey = array_rand($productIds);
//                            $designAdvisorKey = array_rand($designAdvisorIds);
//
//                            $lead = factory(Lead::class)->create([
//                                'sales_contact_id' => $contact->id,
//                                'franchise_id' => $franchise->id,
//                                'lead_source_id' => $leadSourceIds[$leadSourceKey]
//                            ]);
//                            factory(\App\Appointment::class)->create(['lead_id' => $lead->id]);
//                            factory(\App\JobType::class)->create([
//                                'lead_id' => $lead->id,
//                                'product_id' => $productIds[$productKey],
//                                'design_assessor_id' => $designAdvisorIds[$designAdvisorKey]
//                            ]);
//                            factory(\App\Document::class)->create(['lead_id' => $lead->id]);
//                        });
//                }
//            }
//
//        });



    }

}
