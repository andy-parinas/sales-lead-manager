<?php

use App\Franchise;
use App\TradeStaff;
use App\TradeType;
use Illuminate\Database\Seeder;

class TradeStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Builder Contractor',
            'Carpenter',
            'Builder',
            'Patio Fitter',
            'Patio Builder',
            'Construction Manager',
            'Work Supervisor',
            'Chippy',
            'Installer'
        ];

        foreach ($types as $type){
            factory(TradeType::class)->create(['name' => $type]);
        }

        $tradeTypeIds = TradeType::all()->pluck('id')->toArray();

        $franchises = Franchise::all();

        foreach ($franchises as $franchise){

            for ($i = 0; $i <=10; $i++){
                $tradeTypeKey = array_rand($tradeTypeIds);
                factory(TradeStaff::class)->create([
                    'trade_type_id' => $tradeTypeIds[$tradeTypeKey],
                    'franchise_id' => $franchise->id
                ]);
            }

        }





    }
}
