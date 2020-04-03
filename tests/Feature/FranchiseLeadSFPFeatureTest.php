<?php

namespace Tests\Feature;

use App\Franchise;
use App\Lead;
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

        // dd($results->data);
        $this->assertEquals('101', $results->data[0]->number);
        $this->assertEquals('115', end($results->data)->number);
    }
}
