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
        for ($i=101; $i <= 115; $i++) { 
            factory(Lead::class)->create(['number' => strval($i), 'franchise_id' => $franchise->id]);
        }

        $user =  $this->createStaffUser();
        $user->franchises()->attach($franchise->id);
        
        Sanctum::actingAs(
           $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads?sort=number&direction=desc');
        $results = json_decode($response->content());

        // dd($results);
        $this->assertEquals('115', $results->data[0]->number);
        $this->assertEquals('101', end($results->data)->number);

    }

    public function testCanSortLeadByNumberAscending()
    {

        $this->withoutExceptionHandling();

        $franchise = factory(Franchise::class)->create();
        for ($i=101; $i <= 115; $i++) { 
            factory(Lead::class)->create(['number' => strval($i), 'franchise_id' => $franchise->id]);
        }

        $user =  $this->createStaffUser();
        $user->franchises()->attach($franchise->id);
        
        Sanctum::actingAs(
           $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads?sort=number&direction=asc');
        $results = json_decode($response->content());

        dd($results->data);
        $this->assertEquals('101', $results->data[0]->number);
        $this->assertEquals('115', end($results->data)->number);
    }


    public function testCanSearchLead()
    {
        
        $franchise = factory(Franchise::class)->create();
        //Haystack
        for ($i=101; $i <= 115; $i++) { 
            factory(Lead::class)->create(['number' => strval($i), 'franchise_id' => $franchise->id]);
        }

        //Needle
        $customer = factory(SalesContact::class)->create([
            'first_name' => 'Frodo',
            'last_name' => 'Baggins',
            'email' => 'frodo@shire.com',
            'postcode' => '123456'
        ]);

        $lead = factory(Lead::class)->create([
            'number' => '9999999',
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
            'number' => '9999999',
        ];

        foreach ($searchFields as $on => $search) {
            
            $response = $this->get('api/franchises/' . $franchise->id . '/leads?search='. $search . '&on=' . $on);
            $results = json_decode($response->content());

            $response->assertJsonCount(1, 'data');
        }

    }
}
