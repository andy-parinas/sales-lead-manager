<?php

namespace Tests\Feature;

use App\Franchise;
use App\Postcode;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FranchisePostcodeFeatureTest extends TestCase
{

    use RefreshDatabase;

    public function testCanListPostcodeFromAnyFranchiseByHeadOffice()
    {

        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        // Setting the Franchise to belong to Staff User
        // Head Office User should be able read the Postcode for this Franchise
        $user = factory(User::class)->create(['user_type' => User::STAFF_USER ]);
        $franchise = factory(Franchise::class)->create();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        factory(Postcode::class, 5)->create()->each(function($postcode) use ($franchise){
            $franchise->postcodes()->attach($postcode->id);
        });

        $this->get('api/franchises/' . $franchise->id . '/postcodes/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }

    public function testCanOnlyListPostCodeFromFranchiseByNonHeadOffice()
    {
    
        $user = factory(User::class)->create(['user_type' => User::STAFF_USER ]);
        $franchise = factory(Franchise::class)->create();
        $user->franchises()->attach($franchise->id);

        factory(Postcode::class, 5)->create()->each(function($postcode) use ($franchise){
            $franchise->postcodes()->attach($postcode->id);
        });

        Sanctum::actingAs(
            $user,
            ['*']
        );
        
        $this->get('api/franchises/' . $franchise->id . '/postcodes/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');
        
    }

    public function testCanNotListPostCodeOutsideFranchiseByNonHeadOffice()
    {
    
        $user = factory(User::class)->create(['user_type' => User::STAFF_USER ]);
        $franchise = factory(Franchise::class)->create();
        $user->franchises()->attach($franchise->id);

        factory(Postcode::class, 5)->create()->each(function($postcode) use ($franchise){
            $franchise->postcodes()->attach($postcode->id);
        });

        $userOutside = factory(User::class)->create(['user_type' => User::STAFF_USER ]);


        Sanctum::actingAs(
            $userOutside,
            ['*']
        );
        
        $this->get('api/franchises/' . $franchise->id . '/postcodes/')
            ->assertStatus(Response::HTTP_FORBIDDEN);
        
    }


}
