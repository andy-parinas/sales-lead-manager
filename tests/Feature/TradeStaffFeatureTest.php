<?php

namespace Tests\Feature;

use App\TradeStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
