<?php

namespace Tests\Feature;

use App\Franchise;
use App\Lead;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\TestHelper;

class FranchiseLeadSFPFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;

    public function testCanSortLeadByNumberDescending()
    {

        // $this->withoutExceptionHandling();

        $franchise = factory(Franchise::class)->create();
        for ($i=101; $i <= 130; $i++) {
            factory(Lead::class)->create(['lead_number' => strval($i), 'franchise_id' => $franchise->id]);
        }

        $user =  $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
           $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads?sort=leadNumber&direction=desc&size=10');
        $results = json_decode($response->content());

//         dd($results);
        $this->assertEquals('130', $results->data[0]->leadNumber);
        $this->assertEquals('121', end($results->data)->leadNumber);

    }

    public function testCanSortLeadByNumberAscending()
    {

        $this->withoutExceptionHandling();

        $franchise = factory(Franchise::class)->create();
        for ($i=101; $i <= 130; $i++) {
            factory(Lead::class)->create(['lead_number' => strval($i), 'franchise_id' => $franchise->id]);
        }

        $user =  $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
           $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads?sort=leadNumber&direction=asc&size=10');
        $results = json_decode($response->content());

        $this->assertEquals('101', $results->data[0]->leadNumber);
        $this->assertEquals('110', end($results->data)->leadNumber);
    }


    public function testCanSearchLead()
    {

        $franchise = factory(Franchise::class)->create();
        //Haystack
        for ($i=101; $i <= 115; $i++) {
            factory(Lead::class)->create(['lead_number' => strval($i), 'franchise_id' => $franchise->id]);
        }

        //Needle
        $customer = factory(SalesContact::class)->create([
            'first_name' => 'Frodo',
            'last_name' => 'Baggins',
            'email' => 'frodo@shire.com',
            'postcode' => '123456'
        ]);

        $lead = factory(Lead::class)->create([
            'lead_number' => '9999999',
            'franchise_id' => $franchise->id,
            'sales_contact_id' => $customer->id
        ]);

        $user =  $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
           $user,
            ['*']
        );

        $searchFields = [
            'first_name' => 'Frodo',
            'last_name' => 'Baggins',
            'email' => 'frodo@shire.com',
            'postcode' => '123456',
            'lead_number' => '9999999',
        ];

        foreach ($searchFields as $on => $search) {

            $response = $this->get('api/franchises/' . $franchise->id . '/leads?size=10&search='. $search . '&on=' . $on);
            $results = json_decode($response->content());

            $response->assertJsonCount(1, 'data');
        }

    }
}
