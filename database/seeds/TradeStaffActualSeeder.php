<?php

use App\Franchise;
use App\TradeType;
use Illuminate\Database\Seeder;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class TradeStaffActualSeeder extends Seeder
{

    protected $tradeStaffLog;

    public function __construct()
    {
        // Create the logger
        $this->tradeStaffLog = new Logger('variation_logger');
        // Now add some handlers
        $this->tradeStaffLog->pushHandler(new StreamHandler(storage_path() .'/logs/trade_staff.log', Logger::DEBUG));
        $this->tradeStaffLog->pushHandler(new FirePHPHandler());
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $tradeStaffFile = storage_path() . '/app/database/trade_staff_01.csv';


        $file = fopen($tradeStaffFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $tradeName = trim($data[2]);
            $tradeCompany = trim($data[3]);
            $tradePhone = trim($data[4]);
            $tradeFax = trim($data[5]);
            $tradeMobile = trim($data[6]);
            $tradeEmail = trim($data[7]);
            $tradeTypeName = ucfirst(trim($data[8]));
            $abn = trim($data[9]);
            $license = trim($data[10]);
            $isBlocked = intval(trim($data[11]));


            $franchise = Franchise::where('franchise_number', $franchiseNumber)
                            ->where('parent_id', '<>', null)
                            ->first();

            if($franchise != null){

                if($tradeTypeName == "" ){
                    $tradeTypeName = 'Builder';
                }

                $tradeType = TradeType::where('name', 'LIKE', '%' . $tradeTypeName . '%')
                                ->first();

                if($tradeType == null){
                    $tradeType = TradeType::create([
                        'name' => $tradeTypeName
                    ]);

                    $this->tradeStaffLog->info("TradeType Created{$tradeTypeName} Count: {$count} ");
                }


                $nameArray = explode(" ", $tradeName);

                $tradeData = [
                    'first_name' => $nameArray[0],
                    'last_name' => count($nameArray) > 1 ? $nameArray[1] : " ",
                    'email' => $tradeEmail != "" ? $tradeEmail : " ",
                    'contact_number' => "{$tradePhone} {$tradeFax} {$tradeMobile} ",
                    'company' => $tradeCompany,
                    'abn' => $abn,
                    'builders_license' => $license,
                    'status' => $isBlocked != 1 ? 'active' : 'blocked',
                    'franchise_id' => $franchise->id,
                    'trade_type_id' => $tradeType->id
                ];


                $tradeStaff = \App\TradeStaff::create($tradeData);

                $this->tradeStaffLog->info("Trade Staff Created {$tradeStaff->id} Count: {$count} ");
                print "Trade Staff Created {$tradeStaff->id} Count: {$count} \n";


            }else {

                $this->tradeStaffLog->alert("No Franchise Found {$franchiseNumber} Count: {$count} ");
            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;


        }


    }
}
