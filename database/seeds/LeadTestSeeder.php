<?php

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Product;
use App\SalesContact;
use Illuminate\Database\Seeder;

class LeadTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $franchises = Franchise::where('parent_id', '<>', null)->get();

        $franchises->each(function ($franchise) {


            $salesStaff = factory(\App\SalesStaff::class)->create(['franchise_id' => $franchise->id]);


            $postcode = $franchise->postcodes->random();
            $salesContacts = factory(SalesContact::class, 5)->create(['postcode_id' => $postcode->id]);


            foreach ($salesContacts as $salesContact) {

                $leadSource = LeadSource::all()->random();

                $lead = factory(Lead::class)->create([
                    'sales_contact_id' => $salesContact->id,
                    'franchise_id' => $franchise->id,
                    'lead_source_id' => $leadSource->id,
                    'postcode_status' => Lead::INSIDE_OF_FRANCHISE
                ]);

                $product = Product::all()->random();


                factory(JobType::class)->create([
                    'lead_id' => $lead->id,
                    'sales_staff_id' => $salesStaff->id,
                    'product_id' => $product->id
                ]);


                $appointment = factory(Appointment::class)->create(['lead_id' => $lead->id]);

                if ($appointment->outcome == 'success') {


                    $contract = factory(\App\Contract::class)->create([
                        'lead_id' => $lead->id,
                    ]);


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

            }
        });
    }
}
