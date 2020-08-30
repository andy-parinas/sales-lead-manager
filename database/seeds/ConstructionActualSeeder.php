<?php

use App\Construction;
use App\Franchise;
use App\Lead;
use App\TradeStaff;
use Illuminate\Database\Seeder;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ConstructionActualSeeder extends Seeder
{


    protected $constructionLog;

    public function __construct()
    {
        // Create the logger
        $this->constructionLog = new Logger('construction_logger');
        // Now add some handlers
        $this->constructionLog->pushHandler(new StreamHandler(storage_path() .'/logs/construction.log', Logger::DEBUG));
        $this->constructionLog->pushHandler(new FirePHPHandler());
    }



    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $constructionFile = storage_path() . '/app/database/construction_01.csv';


        $file = fopen($constructionFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $tradeStaffData = trim($data[8]);

            if($tradeStaffData != ""){

                $tradeStaffArray = explode(" ", $tradeStaffData);

                $tradeStaff = null;

                if(count($tradeStaffArray) > 1){

                    $tradeStaff = TradeStaff::where('first_name', 'LIKE', '%' . $tradeStaffArray[0] . '%')
                                    ->where('last_name', 'LIKE', '%' . $tradeStaffArray[1] . '%')
                                    ->first();
                }else {

                    $tradeStaff = TradeStaff::where('first_name', 'LIKE', '%' . $tradeStaffArray[0] . '%')
                                     ->first();
                }

                if($tradeStaff != null){

                    $franchiseNumber= trim($data[0]);

                    $franchise = Franchise::where('franchise_number', $franchiseNumber)
                                    ->where('parent_id', '<>', null)
                                    ->first();


                    if($franchise != null){

                        $leadNumber = trim($data[1]);

                        $lead = Lead::where('lead_number', $leadNumber)
                                    ->where('franchise_id', $franchise->id)
                                    ->first();

                        if($lead != null){

                            $salesContact = $lead->salesContact;

                            $constructionData = [
                                'site_address' => $salesContact->street1,
                                'postcode_id' => $salesContact->postcode_id,
                                'trade_staff_id' => $tradeStaff->id,
                                'material_list' => trim($data[2]),
                                'date_materials_received' => trim($data[3]) != "" ? trim($data[3]) : null,
                                'date_assembly_completed' => trim($data[4]) != "" ? trim($data[4]) : null,
                                'date_anticipated_delivery' => trim($data[5]) != "" ? trim($data[5]) : null,
                                'date_finished_product_delivery' => trim($data[6]) != "" ? trim($data[6]) : null,
                                'coil_number' => trim($data[7]),
                                'anticipated_construction_start' => trim($data[9]) != "" ? trim($data[9]) : null,
                                'actual_construction_start' => trim($data[10]) != "" ? trim($data[10]) : null,
                                'anticipated_construction_complete' => trim($data[12]) != "" ? trim($data[12]) : null,
                                'actual_construction_complete' => trim($data[13]) != "" ? trim($data[13]) : null,
                                'final_inspection_date' => trim($data[14]) != "" ? trim($data[14]) : null,
                                'comments' => trim($data[11]),
                            ];

                            $construction = $lead->construction()->create($constructionData);

                            print "Construction Created: {$construction->id} for Lead: {$lead->id} : {$lead->lead_number} \n";
                            $this->constructionLog->alert("Construction Created: {$construction->id} for Lead: {$lead->id} : {$lead->lead_number} Count: {$count}");

                        }else {

                            print "No Lead Found {$leadNumber} with Franchise {$franchiseNumber} \n";
                            $this->constructionLog->alert("No Lead Found {$leadNumber} with Franchise {$franchiseNumber}  Count: {$count}");
                        }

                    }else {

                        print "No Franchise Found. {$franchiseNumber} \n";
                        $this->constructionLog->alert("No Franchise Found. {$franchiseNumber}  Count: {$count}");
                    }



                }else {
                    print "No Trade Staff Found. {$count} \n";
                    $this->constructionLog->alert("No Trade Staff Found. {$tradeStaffData}  Count: {$count}");
                }

            }else {

                print "No Trade Staff encoded. {$count} \n";
            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;

        }


    }
}
