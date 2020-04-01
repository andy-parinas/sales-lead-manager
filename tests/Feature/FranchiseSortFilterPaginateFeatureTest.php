<?php

namespace Tests\Feature;

use App\Franchise;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\TestHelper;

class FranchiseSortFilterPaginateFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;
    
    public function testCanSortByNumberAscending()
    {
        for ($i=101; $i <= 115; $i++) { 
            factory(Franchise::class)->create(['number' => strval($i)]);
        }

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );


        $response = $this->get('api/franchises?sortBy=number&direction=asc&size=15');
        $results = json_decode($response->content());
        // dd(end($results->data)->number);
        $this->assertEquals('101', $results->data[0]->number);
        $this->assertEquals('115', end($results->data)->number);

    }

    public function testCanSortByNumberDescending()
    {
        for ($i=101; $i <= 115; $i++) { 
            factory(Franchise::class)->create(['number' => strval($i)]);
        }

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );


        $response = $this->get('api/franchises?sortBy=number&direction=desc&size=15');
        $results = json_decode($response->content());
        // dd($results->data);
        $this->assertEquals('115', $results->data[0]->number);
        $this->assertEquals('101', end($results->data)->number);
    }

    public function testCanSorByNameAscending()
    {
        foreach (range('A', 'J') as $name) {
            factory(Franchise::class)->create(['name' => $name]);
        }

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );


        $response = $this->get('api/franchises?sortBy=name&direction=asc&size=15');
        $results = json_decode($response->content());
        // dd($results->data);
        $this->assertEquals('A', $results->data[0]->name);
        $this->assertEquals('J', end($results->data)->name);
        
    }

    public function testCanSortByNameDescending()
    {
        foreach (range('A', 'J') as $name) {
            factory(Franchise::class)->create(['name' => $name]);
        }

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );


        $response = $this->get('api/franchises?sortBy=name&direction=desc&size=15');
        $results = json_decode($response->content());
        // dd($results->data);
        $this->assertEquals('J', $results->data[0]->name);
        $this->assertEquals('A', end($results->data)->name);
    }

}
