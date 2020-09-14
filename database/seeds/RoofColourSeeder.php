<?php

use Illuminate\Database\Seeder;

class RoofColourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roofSheets = [
            'Ultraspan 0.48',
            'Xpro 0.42',
            'Prospan 0.42',
            'Multi Roof 0.42',
            'Ezbuild 0.42',
            'Climatek 50mm',
            'Climatek 90mm'
        ];


        foreach ($roofSheets as $roofSheet){

            \App\RoofSheet::create([
                'name' => $roofSheet
            ]);
            print "Roof Sheet {$roofSheet} created \n";
        }

    }
}
