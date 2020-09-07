<?php

namespace Tests\Feature;

use App\Franchise;
use App\SalesStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class SalesStaffFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanListSalesStaff()
    {
        $this->withoutExceptionHandling();

        $user = $this->createStaffUser();

        $franchise =factory(Franchise::class)->create();

        $user->franchises()->attach($franchise->id);

        factory(SalesStaff::class, 5)->create([
            'status' => SalesStaff::ACTIVE,
            'franchise_id' => $franchise->id
        ]);

        factory(SalesStaff::class, 5)->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('api/sales-staffs?size=10');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }

    public function testCanSearchSalesStaff()
    {
        $this->withoutExceptionHandling();

        //haystack
        factory(SalesStaff::class, 15)->create();

        factory(SalesStaff::class)->create([
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'atparinas@gmail.com'
        ]);

        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/sales-staffs/search/?search=Andy');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK);
    }

}
