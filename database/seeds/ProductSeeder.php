<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $poducts = [
            'Patio', 'Gable Patio', 'Carport', 'Gable Carport',
            'Screen Room', 'Glass Room', 'Enclosed Under', 'Deck',
            'Covered Deck', 'Opening Roof - Concertina', 'Opening Roof - Silencio',
            'Climatek 50mm', 'Climatek 75mm', 'Climatek 90mm', 'Supply Only', 'Insurance Claim',
            'Commercial', 'Weatherlite', 'Concretes/Paving', 'Handrails', 'Other'
        ];


        foreach ($poducts as $product){
            factory(\App\Product::class)->create(['name' => $product]);
        }

    }
}
