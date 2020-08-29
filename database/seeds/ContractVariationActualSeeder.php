<?php

use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ContractVariationActualSeeder extends Seeder
{

    protected $variationLog;

    public function __construct()
    {
        // Create the logger
        $this->variationLog = new Logger('variation_logger');
        // Now add some handlers
        $this->variationLog->pushHandler(new StreamHandler(storage_path() .'/logs/variation.log', Logger::DEBUG));
        $this->variationLog->pushHandler(new FirePHPHandler());
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contractFile = storage_path() . '/app/database/contracts_variation_01.csv';


        $file = fopen($contractFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $variationDate = trim($data[4]) != "" ? trim($data[4])  : date("Y-m-d");
            $description = trim($data[5]);
            $amount = floatval(trim($data[6]));

            $franchiseNumber = trim($data[0]);
            $leadNumber = trim($data[1]);

            $franchise = Franchise::where('franchise_number', $franchiseNumber)
                ->where('parent_id', '<>', null)
                ->first();

            if($franchise != null){

                $lead = Lead::where('lead_number', $leadNumber)
                    ->where('franchise_id', $franchise->id)
                    ->first();


                if($lead != null) {

                    $contract = $lead->contract;

                    if($contract != null){

                            $data = [
                                'variation_date' => $variationDate,
                                'description' => $description,
                                'amount' => $amount,
                            ];

                            $variation = $contract->contractVariations()->create($data);

                            $total_variation = $contract->total_variation + $variation->amount;
                            $total_contract = $contract->total_contract + $variation->amount;

                            $contract->update([
                                'total_variation' => $total_variation,
                                'total_contract' => $total_contract
                            ]);

                            print "Variation Created and Contract Updated For {$contract->id} \n";
                            $this->variationLog->info("Variation Created and Contract Updated For {$contract->id}");

                    }else {

                        $this->variationLog->alert("No Contract Found for  {$lead->lead_number} LeadId: {$lead->id} Count: {$count} ");
                    }

                }else {
                    $this->variationLog->alert("No Lead Found {$leadNumber} Count: {$count} ");
                }

            }else {
                $this->variationLog->alert("No Franchise Found {$franchiseNumber} Count: {$count} ");
            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;

        }
    }
}
