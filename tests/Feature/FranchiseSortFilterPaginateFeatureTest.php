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
            factory(Franchise::class)->create(['franchise_number' => strval($i)]);
        }

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );


        $response = $this->get('api/franchises?sort=number&direction=asc&size=10');
        $results = json_decode($response->content());

        $this->assertEquals('101', $results->data[0]->franchiseNumber);
        $this->assertEquals('110', end($results->data)->franchiseNumber);

    }

    public function testCanSortByNumberDescending()
    {
        for ($i=101; $i <= 115; $i++) {
            factory(Franchise::class)->create(['franchise_number' => strval($i)]);
        }

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );


        $response = $this->get('api/franchises?sort=franchise_number&direction=desc&size=10');
        $results = json_decode($response->content());
        $this->assertEquals('115', $results->data[0]->franchiseNumber);
        $this->assertEquals('106', end($results->data)->franchiseNumber);
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


        $response = $this->get('api/franchises?sort=name&direction=asc&size=15');
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


        $response = $this->get('api/franchises?sort=name&direction=desc&size=15');
        $results = json_decode($response->content());
        // dd($results->data);
        $this->assertEquals('J', $results->data[0]->name);
        $this->assertEquals('A', end($results->data)->name);
    }

    public function testCanSortFranchiseQueriedFromUser()
    {
        $user = $this->createFranchiseAdminUser();

        foreach (range('A', 'J') as $name) {
            $franchise = factory(Franchise::class)->create(['name' => $name]);
            $user->franchises()->attach($franchise->id);
        }


        Sanctum::actingAs(
            $user,
            ['*']
        );


        $response = $this->get('api/franchises?sort=name&direction=asc&size=15');
        $results = json_decode($response->content());
        // dd($results->data);
        $this->assertEquals('A', $results->data[0]->name);
        $this->assertEquals('J', end($results->data)->name);

        $response = $this->get('api/franchises?sort=name&direction=desc&size=15');
        $results = json_decode($response->content());
        // dd($results->data);
        $this->assertEquals('J', $results->data[0]->name);
        $this->assertEquals('A', end($results->data)->name);

    }


    public function testCanSearchFranchiseByHeadOffice()
    {

        //Haystack
        factory(Franchise::class, 5)->create();

        //Needles
       factory(Franchise::class)->create(['name' => 'AAAAAAA', 'franchise_number' => '111111']);
       factory(Franchise::class)->create(['name' => 'AAAAABB', 'franchise_number' => '111122']);
       factory(Franchise::class)->create(['name' => 'AAAAACC', 'franchise_number' => '333322']);

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $this->get('api/franchises?search=AAAA&on=name')
            ->assertJsonCount(3, 'data');

        $this->get('api/franchises?search=1111&on=franchise_number')
            ->assertJsonCount(2, 'data');


    }

    public function testCanSearchFranchiseByFranchiseAdmin()
    {
        $user = $this->createFranchiseAdminUser();

        //Haystack
        factory(Franchise::class, 5)->create()->each(function($franchise) use ($user){
            $user->franchises()->attach($franchise->id);
        });

        //Needles
       factory(Franchise::class)->create(['name' => 'AAAAAAA', 'franchise_number' => '111111'])->users()->attach($user->id);
       factory(Franchise::class)->create(['name' => 'AAAAABB', 'franchise_number' => '111122'])->users()->attach($user->id);
       factory(Franchise::class)->create(['name' => 'AAAAACC', 'franchise_number' => '333322'])->users()->attach($user->id);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->get('api/franchises?search=AAAA&on=name')
            ->assertJsonCount(3, 'data');

        $this->get('api/franchises?search=1111&on=franchise_number')
            ->assertJsonCount(2, 'data');
    }


}
