<?php

use App\Franchise;
use App\Postcode;
use Illuminate\Database\Seeder;

class FranchiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

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

    }
}
