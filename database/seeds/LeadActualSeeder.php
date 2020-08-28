<?php

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\Product;
use App\SalesContact;
use App\SalesStaff;
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
                    print "SalesContact Created \n";
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

                $lead = Lead::create($leadData);
                print "####### Lead Created ######## \n";


                /**
                 * JobType Seeder
                 */

                $nameArray = explode(" ", trim($data[19]));
                $designAdvisorFirstName = $nameArray[0];
                $designAdvisorLastName = count($nameArray) > 1 ? $nameArray[1] : " ";

                $designAdvisor = SalesStaff::where('first_name', $designAdvisorFirstName)
                                    ->where('last_name', $designAdvisorLastName)->first();

                if($designAdvisor){

                    $product = Product::where('name', 'LIKE', '%' . trim($data[20]) . '%')->first();

                    if($product == null){
                        $product = Product::create(['name' => trim($data[20])]);
                    }

                    $jobTypeData = [
                        'taken_by' => trim($data[15]),
                        'date_allocated' => trim($data[17]),
                        'sales_staff_id' => $designAdvisor->id,
                        'lead_id' => $lead->id,
                        'description' => trim($data[21]),
                        'product_id' => $product->id
                    ];

                    JobType::create($jobTypeData);
                    print "Lead Job Type Create \n";

                }else {
                    Log::alert("Sales Staff Not found {$designAdvisorFirstName}");
                    print "Sales Staff Not found\n";
                }

                /**
                 * Appointment Seeder
                 */

                $appointmentDate = trim($data[22]);
                $reAppointmentDate = trim($data[24]);

                $actualAppointmentDate = date("Y-m-d");

                if($reAppointmentDate != "") {
                    $actualAppointmentDate = $reAppointmentDate;
                }elseif ($appointmentDate !== ""){
                    $actualAppointmentDate = $appointmentDate;
                }

                $outcome = trim($data[28]) != "" ? strtolower(trim($data[28])) : "pending";

                $appointmentData = [
                    'appointment_date' => $actualAppointmentDate,
                    'lead_id' => $lead->id,
                    'outcome' => $outcome,
                    'comments' => trim($data[26]),
                    'quoted_price' => floatval(trim($data[27]))
                ];

                Appointment::create($appointmentData);
                print "Lead Appointment Created \n";

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
