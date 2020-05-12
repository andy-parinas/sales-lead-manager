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

        $franchiseAdmin = factory(User::class)->create([
            'username' => 'franchiseadmin1',
            'user_type' => User::FRANCHISE_ADMIN
        ]);

        Franchise::all()->each(function ($franchise) use ($franchiseAdmin)  {
            $franchiseAdmin->franchises()->attach($franchise->id);

            if(!$franchise->isParent()){
                dump('Creating Staff User');
                $user = factory(User::class)->create([
                    'username' => 'staffuser' . $franchise->id,
                    'user_type' => User::STAFF_USER
                ]);
                $user->franchises()->attach($franchise->id);
            }

        });

    }
}