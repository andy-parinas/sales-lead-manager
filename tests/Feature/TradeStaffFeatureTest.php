<?php

namespace Tests\Feature;

use App\Franchise;
use App\TradeStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class TradeStaffFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function testCanListTradeStaffs()
    {
        $this->authenticateHeadOfficeUser();

        factory(TradeStaff::class, 15)->create();

        $response = $this->get('api/trade-staffs?size=10');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');


    }

    public function testCanSearchTradeStaff()
    {
        $this->withoutExceptionHandling();


        //Haystack
        factory(TradeStaff::class, 10)->create();

        //Needle
        factory(TradeStaff::class)->create([
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'atparinas@gmail.com'
        ]);

        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/trade-staffs/search?search=andy');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');


    }


    public function testCanListTradeStaffUnderUserFranchise()
    {

        $user = $this->createStaffUser();

        $franchise = factory(Franchise::class)->create();

        $user->franchises()->attach($franchise->id);

        factory(TradeStaff::class, 5)->create([
            'franchise_id' => $franchise->id,
            'status' => TradeStaff::ACTIVE
        ]);

        factory(TradeStaff::class)->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('api/trade-staffs?size=10');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');



    }
}
