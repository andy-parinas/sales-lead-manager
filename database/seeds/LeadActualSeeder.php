<?php

use App\Franchise;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\SalesContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class LeadActualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $leadFile = storage_path() . '/app/database/leads_01.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $contactFirstName = trim($data[3]);
            $contactLastName = trim($data[4]);
            $contactEmail = trim($data[14]) != "" ? trim($data[14]) : 'noemail@email.com';

            $contactNumber = "";

            if(trim($data[11]) != "" ) $contactNumber .= trim($data[11]) . " ";
            if(trim($data[12]) != "" ) $contactNumber .= trim($data[11]) . " ";
            if(trim($data[13]) != "" ) $contactNumber .= trim($data[11]) . " ";


            $postcode = Postcode::where('pcode', trim($data[10]))->first();
            if($postcode == null){
                print "Postcode is Missing {$data[10]}";
            }

            $salesContact = SalesContact::where('first_name', $contactFirstName)
                                ->where('last_name', $contactLastName)->first();


            if($salesContact == null && $postcode != null){
                $salesContactData = [
                    'first_name' => $contactFirstName,
                    'last_name' => $contactLastName,
                    'title' => trim($data[5]),
                    'street1' => trim($data[6]),
                    'street2' => trim($data[7]),
                    'postcode_id' => $postcode->id,
                    'customer_type' => strtolower(trim($data[18])),
                    'contact_number' => $contactNumber,
                    'email' => $contactEmail
                ];
                try {
                    $salesContact = SalesContact::create($salesContactData);
                    print "SalesContact Created";
                }catch (Exception $exception){
                    print "Failed Creating Sales Contact";
                    Log::error("Error Creating Sales Contact {$contactFirstName} {$contactLastName}");
                }


            }else {

                print "Sales Contact Already Exist \n";
            }

            $franchise = Franchise::where('franchise_number', trim($data[0]))
                            ->where('parent_id', '<>', null)->first();

            $leadSourceName = trim($data[16]);

            $leadSource = LeadSource::where('name', 'LIKE',  '%' . $leadSourceName . '%' )->first();

            if($leadSource == null){
                $leadSource = LeadSource::create(['name' => $leadSourceName]);
            }

            if($franchise != null && $leadSource != null && $salesContact != null){

                $leadData = [
                    'lead_number' => trim($data[1]),
                    'franchise_id' => $franchise->id,
                    'sales_contact_id' => $salesContact->id,
                    'lead_source_id' => $leadSource->id,
                    'lead_date' => trim($data[2]),
                    'postcode_status' => Lead::INSIDE_OF_FRANCHISE
                ];

//                try {
//                    Lead::create($leadData);
//                    print "####### Lead Created ######## \n";
//                }catch (Exception $exception){
//                    Log::error("Unable to create Lead {$data[1]}");
//                }

                Lead::create($leadData);
                print "####### Lead Created ######## \n";



            }else {

                print "\n#### Franchise Does Not Exist {$data[0]} ########### \n";
                Log::alert("Franchise Does Not Exist {$data[0]}");
            }

            print "############# Item number {$count} ############## \n";
            $count++;

        }

        fclose($file);
    }
}
