<?php

use App\Franchise;
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create a HeadOffice user
        factory(User::class)->create([
            'username' => 'headoffice1',
            'user_type' => User::HEAD_OFFICE
        ]);


        $mainFranchises = Franchise::where('parent_id', null)->get();

        $mainFranchises->each(function ($main){

            $franchiseAdmin = factory(User::class)->create([
                    'username' => 'franchiseadmin-' . $main->franchise_number,
                    'user_type' => User::FRANCHISE_ADMIN
            ]);

            $franchiseAdmin->franchises()->attach($main->id);

            $main->children->each(function ($sub) use ($franchiseAdmin) {

                $user = factory(User::class)->create([
                    'username' => 'staffuser-' . $sub->franchise_number,
                    'user_type' => User::STAFF_USER
                ]);
                $user->franchises()->attach($sub->id);
                $franchiseAdmin->franchises()->attach($sub->id);

            });

        });


//
//        $franchiseAdmin = factory(User::class)->create([
//            'username' => 'franchiseadmin1',
//            'user_type' => User::FRANCHISE_ADMIN
//        ]);

//        Franchise::all()->each(function ($franchise) {
//            //$franchiseAdmin->franchises()->attach($franchise->id);
//
//            if(!$franchise->isParent()){
//                dump('Creating Staff User');
//                $user = factory(User::class)->create([
//                    'username' => 'staffuser-' . $franchise->franchise_number,
//                    'user_type' => User::STAFF_USER
//                ]);
//                $user->franchises()->attach($franchise->id);
//            }else {
//
//                $franchiseAdmin = factory(User::class)->create([
//                    'username' => 'franchiseadmin-' . $franchise->franchise_number,
//                    'user_type' => User::FRANCHISE_ADMIN
//                ]);
//                $franchiseAdmin->franchises()->attach($franchise->id);
//            }
//
//        });

    }
}
