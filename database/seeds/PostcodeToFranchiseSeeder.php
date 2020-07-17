<?php

use App\Franchise;
use App\Postcode;
use Illuminate\Database\Seeder;

class PostcodeToFranchiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $postcodeFile = storage_path() . '/app/database/postcode-to-franchise.json';

        $strJsonFileContents = file_get_contents($postcodeFile);
        $postcodeArray = json_decode($strJsonFileContents, false);

        foreach ($postcodeArray as $postcode){

            $post = Postcode::where('pcode', $postcode->Postcode)
                        ->where('locality', $postcode->Locality)
                        ->first();

            $subFranchise = Franchise::where('franchise_number', $postcode->sub_franchise_code)
                            ->where('parent_id', '<>', null)
                            ->first();

            $mainFranchise = Franchise::where('franchise_number', $postcode->main_franchise_code)
                                ->where('parent_id', null)
                                ->first();


            if($post != null && $subFranchise != null && $mainFranchise != null){
                print "Postcode: " . $post->pcode . " PostcodeId: " . $post->id .
                    " | Sub-Franchise: " . $subFranchise->franchise_number . " Sub-FranchiseId: ". $subFranchise->id . "\n";

                print "Postcode: " . $post->pcode . " PostcodeId: " . $post->id .
                    " | Main-Franchise: " . $mainFranchise->franchise_number . " Main-FranchiseId: ". $mainFranchise->id . "\n";

                $subFranchise->postcodes()->attach($post->id);
                $mainFranchise->postcodes()->attach($post->id);

            }

        }



    }
}
