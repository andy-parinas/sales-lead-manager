<?php

use App\Postcode;
use Illuminate\Database\Seeder;

class PostcodeFullSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $postcodeFile = storage_path() . '/app/database/postcodes.csv';

        $file = fopen($postcodeFile, 'r');

        print("Creating Postcodes ");
        while(($line = fgetcsv($file)) !== false){
            print (".");
            factory(Postcode::class)->create([
                'pcode' => $line[0],
                'locality' => $line[1],
                'state' => $line[2],
                'comments' => $line[3],
                'delivery_office' => $line[4],
                'presort_indicator' => $line[5],
                'parcel_zone' => $line[6],
                'bsp_number' => $line[7],
                'bsp_name' => $line[8],
                'category' => $line[9],
            ]);
        }


        fclose($file);
        print(". \n");
        print("Creating Postcodes Completed ");
    }
}
