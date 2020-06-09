<?php

use App\Franchise;
use App\SalesStaff;
use Illuminate\Database\Seeder;

class SalesStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $franchises = Franchise::all();

        $franchises->each(function ($franchise){
            dump('Creating Sales Staff');
            if($franchise->isParent() && $franchise->children()->count() == 0){
                factory(SalesStaff::class, 10)->create(['franchise_id' => $franchise->id]);
            }elseif (!$franchise->isParent()){
                factory(SalesStaff::class, 10)->create(['franchise_id' => $franchise->id]);
            }
        });
    }
}
