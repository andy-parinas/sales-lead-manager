<?php

namespace Tests\Feature;

use App\SalesStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class SalesStaffFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanListSalesStaff()
    {
        factory(SalesStaff::class, 15)->create();

        $this->authenticateStaffUser();

        $response = $this->get('api/sales-staffs?size=10');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }

}
