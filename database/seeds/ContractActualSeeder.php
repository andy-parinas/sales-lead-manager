<?php

use App\Lead;
use Illuminate\Database\Seeder;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ContractActualSeeder extends Seeder
{

    protected $contractLog;

    public function __construct()
    {
        // Create the logger
        $this->contractLog = new Logger('contract_logger');
        // Now add some handlers
        $this->contractLog->pushHandler(new StreamHandler(storage_path() .'/logs/contract.log', Logger::DEBUG));
        $this->contractLog->pushHandler(new FirePHPHandler());
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contractFile = storage_path() . '/app/database/contracts_01.csv';


        $file = fopen($contractFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $franchise = trim($data[0]);
            $leadNumber = trim($data[1]);
            $contractDate = trim($data[2]);
            $contractNumber = trim($data[3]);
            $contractPrice = floatval(trim($data[4]));
            $warrantyRequired = trim($data[5]);
            $deposit = floatval(trim($data[7]));
            $dateDepositReceived = trim($data[8]);
            $dateWarrantySent = trim($data[6]);

            $data = [
                'contract_date' => $contractDate,
                'contract_number' => $contractNumber,
                'contract_price' =>  $contractPrice,
                'deposit_amount' =>  $deposit,
                'date_deposit_received' => $dateDepositReceived != "" ?  $dateDepositReceived : null,
                'total_contract' => $contractPrice,
                'warranty_required' => $warrantyRequired == 1? 'yes' : 'no',
                'date_warranty_sent' => $dateWarrantySent != "" ? $dateWarrantySent : null
            ];

            $lead = Lead::where('lead_number', $leadNumber)->first();

            if($lead != null){

                $lead->contract()->create($data);

                print "Contract Created For {$lead->lead_number} Count: {$count} ";
                $this->contractLog->info("Contract Created For {$lead->lead_number} Count: {$count} ");

            }else{

                print "No Lead Found Lead: {$leadNumber}, Franchise: {$franchise} Count: {$count} \n";
                $this->contractLog->alert("No Lead Found {$leadNumber} Count: {$count} ");

            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;

        }


    }
}
