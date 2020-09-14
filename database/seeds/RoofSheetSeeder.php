<?php

use Illuminate\Database\Seeder;

class RoofSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roofColours = [
            'Classic Cream/Classic Cream',
            'Classic Cream/Manor Red',
            'Classic Cream/Wilderness',
            'Classic Cream/Woodland Grey',
            'Classic Cream/Dune',
            'Dover White/Dover White',
            'Dover White/Dune 80/55',
            'Dover White/Surfmist',
            'Cottage Green/Classic Cream',
            'Birch/Thredbo White',
            'Paperbank/Papaerbank',
            'Off White/Birch',
            'Resin Coated Aluzinc'
        ];

        foreach ($roofColours as $roofColour){

            \App\RoofColour::create([
                'name' => $roofColour
            ]);

            print "Roof Colour {$roofColour} Created \n";
        }
    }
}
