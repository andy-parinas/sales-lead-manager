<?php

use App\Franchise;
use App\SalesStaff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SalesStaffActualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $leadFile = storage_path() . '/app/database/sales_staff.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $salesFirstName = trim($data[2]);
            $salesLastName = trim($data[3]);
            $salesEmail = trim($data[7]);
            $salesEmail2 = trim($data[8]);
            $salesNumber = trim($data[6]);
            $salesPhone = trim($data[5]);
            $salesFranchise = trim($data[0]);

            $salesName = trim($data[4]);


            $franchise = Franchise::where('franchise_number', (string)$salesFranchise)
                ->where('parent_id', '<>', null)->first();

            if($franchise != null){

                $salesStaff = SalesStaff::where('first_name', $salesFirstName)
                                ->where('last_name', $salesLastName)->first();

                if($salesStaff == null){

                    $data = [
                        'first_name' => $salesFirstName,
                        'last_name' => $salesLastName,
                        'legacy_name' => $salesName,
                        'contact_number' => $salesNumber,
                        'sales_phone' => $salesPhone,
                        'email' => $salesEmail,
                        'email2' => $salesEmail2,
                        'status' => SalesStaff::ACTIVE,
                    ];

                    $salesStaff = SalesStaff::create($data);
                    print "Sales staff Created {$salesName} \n";

                    $salesStaff->franchises()->attach($franchise->id);
                    print "Franchise Assigned {$salesFranchise} \n";

                }else {
                    $salesStaff->franchises()->attach($franchise->id);
                    print "Sales Staff Exist {$salesName} - Franchise Assigned {$salesFranchise} \n";
                }


            }else {
                print "No Sales Staff Created - Franchise Not Found {$salesFranchise} \n";
            }


        }
    }
}
