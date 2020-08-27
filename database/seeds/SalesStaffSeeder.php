<?php

use App\Franchise;
use App\SalesStaff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SalesStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * This is the Actual Seeder
         */

        $salesStaffFile = storage_path() . '/app/database/sales_staff.json';

        $strJsonFileContents = file_get_contents($salesStaffFile);
        $salesStaffArray = json_decode($strJsonFileContents, false);


        print("Creating Sales Staff ");
        foreach ($salesStaffArray as $salesStaff){

            $nameArray = explode(" ",$salesStaff->Sales_Name );
            $firstName = $nameArray[0];
            $lastName = count($nameArray) > 1 ? $nameArray[1] : "";

            $status = SalesStaff::ACTIVE;

            if($salesStaff->Blocked == 1){
                $status = SalesStaff::BLOCKED;
            }

            $franchise = Franchise::where('franchise_number', (string)$salesStaff->Franchise)
                            ->where('parent_id', '<>', null)->first();

            if($franchise != null){

                $data = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'contact_number' => $salesStaff->Sales_Phone . ' / ' . $salesStaff->Sales_Mob,
                    'email' => $salesStaff->Sales_Email,
                    'status' => $status,
                    'franchise_id' => $franchise->id
                ];

                SalesStaff::create($data);
                print "Sales staff Created \n";

            }else {
                Log::alert("SalesStaffSeeder: Franchise not found {$salesStaff->Franchise}");
            }

        }


    }
}
