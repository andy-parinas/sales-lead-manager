<?php

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\Product;
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
//        $this->call([
//            FranchiseSeeder::class,
//            PostcodeSeeder::class,
//            UserSeeder::class,
//            ProductSeeder::class,
//            LeadSourceSeeder::class,
//            LeadSeeder::class
//        ]);

        $franchiseFile = storage_path() . '/app/database/franchise.json';

        $strJsonFileContents = file_get_contents($franchiseFile);
        $array = json_decode($strJsonFileContents, false);

        $franchises = $array->franchises;


        foreach ($franchises as $franchise){

            print ("Creating Main Franchise \n");
            $newFranchise = Franchise::create([
                'franchise_number' => $franchise->franchise_number,
                'name' => $franchise->name
            ]);

            foreach ($franchise->children as $child){
                print ("Creating Sub Franchise \n");
                Franchise::create([
                    'franchise_number' => $child->franchise_number,
                    'name' => $child->name,
                    'parent_id' => $newFranchise->id
                ]);
            }
        }


        ############ POSTCODE ##########################

        $postcodeFile = storage_path() . '/app/database/postcodes.json';

        $strJsonFileContents = file_get_contents($postcodeFile);
        $postcodeArray = json_decode($strJsonFileContents, false);


        print("Creating Postcodes ");
        foreach ($postcodeArray as $postcode){

            print (".");

            Postcode::create([
                'pcode' => $postcode->pcode,
                'locality' => $postcode->locality,
                'state' => $postcode->state,
                'comments' => $postcode->comments,
                'delivery_office' => $postcode->delivery_office,
                'presort_indicator' => $postcode->presort_indicator,
                'parcel_zone' => $postcode->parcel_zone,
                'bsp_number' => $postcode->bsp_number,
                'bsp_name' => $postcode->bsp_name,
                'category' => $postcode->category,
            ]);

        }

        $postcodeFranchiseFile = storage_path() . '/app/database/postcode-to-franchise.json';

        $strJsonFileContents = file_get_contents($postcodeFranchiseFile);
        $postcodeArray = json_decode($strJsonFileContents, false);

        foreach ($postcodeArray as $postcode){

            $post = Postcode::where('pcode', $postcode->Postcode)
                ->where('locality', $postcode->Locality)
                ->first();

            $subFranchise = Franchise::where('franchise_number', $postcode->sub_franchise_code)
                ->where('parent_id', '<>', null)
                ->first();

            $mainFranchise = Franchise::where('franchise_number', $postcode->main_franchise_code)
                ->where('parent_id', null)
                ->first();


            if($post != null && $subFranchise != null && $mainFranchise != null){
                print "Postcode: " . $post->pcode . " PostcodeId: " . $post->id .
                    " | Sub-Franchise: " . $subFranchise->franchise_number . " Sub-FranchiseId: ". $subFranchise->id . "\n";

                print "Postcode: " . $post->pcode . " PostcodeId: " . $post->id .
                    " | Main-Franchise: " . $mainFranchise->franchise_number . " Main-FranchiseId: ". $mainFranchise->id . "\n";

                $subFranchise->postcodes()->attach($post->id);
                $mainFranchise->postcodes()->attach($post->id);

            }

        }


        ################################ USER ####################

        //Create a HeadOffice user
        factory(User::class)->create([
            'username' => 'headoffice1',
            'user_type' => User::HEAD_OFFICE
        ]);


        $mainFranchises = Franchise::where('parent_id', null)->get();

        $mainFranchises->each(function ($main){

            $franchiseAdmin = factory(User::class)->create([
                'username' => 'franchiseadmin-' . $main->franchise_number,
                'user_type' => User::FRANCHISE_ADMIN
            ]);

            $franchiseAdmin->franchises()->attach($main->id);

            $main->children->each(function ($sub) use ($franchiseAdmin) {

                $user = factory(User::class)->create([
                    'username' => 'staffuser-' . $sub->franchise_number,
                    'user_type' => User::STAFF_USER
                ]);
                $user->franchises()->attach($sub->id);
                $franchiseAdmin->franchises()->attach($sub->id);

            });

        });


        ############################# PRODUCT #################

        $poducts = [
            'Patio', 'Gable Patio', 'Carport', 'Gable Carport',
            'Screen Room', 'Glass Room', 'Enclosed Under', 'Deck',
            'Covered Deck', 'Opening Roof - Concertina', 'Opening Roof - Silencio',
            'Climatek 50mm', 'Climatek 75mm', 'Climatek 90mm', 'Supply Only', 'Insurance Claim',
            'Commercial', 'Weatherlite', 'Concretes/Paving', 'Handrails', 'Other'
        ];


        foreach ($poducts as $product){
            factory(\App\Product::class)->create(['name' => $product]);
        }


        ############# LEAD SOURCE ###################

        $sources = [
            'Newspaper', 'Magazine', 'Radio',
            'Self Genrated', 'Website', 'Friend',
            'Yello Pages', 'Yellow Pages Online',
            'Refferal', 'Pamphlets', 'Walk-In',
            'Return Customer', 'Home Show', 'Sit Sign',
            'Local Directory', 'Trade Contact', 'Facbook',
            'Google or Search Engine', 'Insurance Company', 'Local Newspaper',
            'Vehicle Sign', 'Hi Pages', 'Chronicle', 'Shopping Centre', 'Direct Mail Out',
            'DL Intro Card', 'Ag Show', 'Facebook', 'Twitter', 'LinkedIn', 'Instagram', 'Unknown'
        ];

        foreach ($sources as $source){
            factory(LeadSource::class)->create(['name' => $source]);
        }


        ################# LEAD ############################

        $franchises = Franchise::where('parent_id', '<>', null)->get();

        $franchises->each(function ($franchise){

            print "Creating 5 Sales Staff \n";
            $salesStaffs = factory(\App\SalesStaff::class, 5)->create(['franchise_id' => $franchise->id]);

            print "Creating 5 Trade Staff \n";
            $tradeStaffs = factory(\App\TradeStaff::class, 5)->create(['franchise_id' => $franchise->id]);

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

                $salesStaff = $salesStaffs->random();

                print "Creating Job Type \n";
                factory(JobType::class)->create([
                    'lead_id' => $lead->id,
                    'sales_staff_id' => $salesStaff->id,
                    'product_id' => $product->id
                ]);

                print "Creating Appointment \n";
                $appointment = factory(Appointment::class)->create(['lead_id' => $lead->id]);

                if($appointment->outcome == 'success'){

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

                    print "Creating Constructions \n";
                    $tradeStaff = $tradeStaffs->random();

                    factory(\App\Construction::class)->create([
                        'trade_staff_id' => $tradeStaff->id,
                        'postcode_id' =>  $postcode->id
                    ]);

                }

            }

        });

    }
}
