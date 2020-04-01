<?php

namespace Tests\Feature;

use App\Franchise;
use App\Lead;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class FranchiseFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function testCanListAllFranchiseByHeadOffice()
    {
        $this->withoutExceptionHandling();
        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        // Create a Franchise that is Under the Staff User
        // This should be listed by the Head Office User
        factory(Franchise::class, 5)->create()->each(function($franchise){
            $user = factory(User::class)->create(['user_type' => User::STAFF_USER]);
            $user->franchises()->attach($franchise->id);
        });

        $response = $this->get('api/franchises');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(5, 'data');

    }


    public function testCanListFranchiseUnderUsersFranchise()
    {
        // Needle
        $user1 = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        factory(Franchise::class, 7)->create()->each(function($franchise) use ($user1){
            $user1->franchises()->attach($franchise->id);
        });


        //Haystack - This should not be viewed by $user1
        $user2 = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        factory(Franchise::class, 3)->create()->each(function($franchise) use ($user2){
            $user2->franchises()->attach($franchise->id);
        });

        Sanctum::actingAs(
            $user1,
            ['*']
        );

        $response = $this->get('api/franchises');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(7, 'data');

    }

    public function testCanShowFranchiseUnderUsersFranchise()
    {

        // $this->withoutExceptionHandling();

        $user = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        $franchise = factory(Franchise::class)->create();

        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id);
        $response->assertStatus(Response::HTTP_OK);

    }

    public function testCanNotShowFranchiseUnderUsersFranchise()
    {

        $user = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

    }


    public function testCanShowFranchiseByHeadOffice()
    {
        $user = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id);
        $response->assertStatus(Response::HTTP_OK);

    }

    public function testCanOnlyCreateFranchiseByHeadOffice()
    {
        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::HEAD_OFFICE]),
            ['*']
        );

        $franchiseData = factory(Franchise::class)->raw();

        $this->post('api/franchises', $franchiseData)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, Franchise::all());
    }


    public function testCanNotCreateFranchiseByNonHeadOffice()
    {

        $franchiseData = factory(Franchise::class)->raw();


        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]),
            ['*']
        );

        $this->post('api/franchises', $franchiseData)
        ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, Franchise::all());

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::STAFF_USER]),
            ['*']
        );

        $this->post('api/franchises', $franchiseData)
        ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, Franchise::all());
        
    }


    public function testCanUpdateFranchiseByHeadOffice()
    {
        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::HEAD_OFFICE]),
            ['*']
        );

        $this->put('api/franchises/' . $franchise->id, ['number' => 'updated'])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals('updated', Franchise::first()->number);


    }

    public function testCanNotUpdateFranchiseByNonHeadOffice()
    {
        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]),
            ['*']
        );

        $this->put('api/franchises/' . $franchise->id, ['number' => 'updated'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertEquals($franchise->number, Franchise::first()->number);

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::STAFF_USER]),
            ['*']
        );

        $this->put('api/franchises/' . $franchise->id, ['number' => 'updated'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertEquals($franchise->number, Franchise::first()->number);


    }



    public function testCanDeleteFranchiseByHeadOffice()
    {

        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::HEAD_OFFICE]),
            ['*']
        );

        $this->delete('api/franchises/' . $franchise->id)
            ->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, Franchise::all());

    }

    public function testCanNotDeleteFranchiseByNonHeadOffice()
    {
        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]),
            ['*']
        );

        $this->delete('api/franchises/' . $franchise->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Franchise::all());

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::STAFF_USER]),
            ['*']
        );

        $this->delete('api/franchises/' . $franchise->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Franchise::all());

    }

    public function testCanNoDeleteFranchiseWithRelatedData()
    {
        $franchise = factory(Franchise::class)->create();
        factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $this->delete('api/franchises/' . $franchise->id)
            ->assertStatus(Response::HTTP_CONFLICT);
        $this->assertCount(1, Franchise::all());

        // dump(Lead::first()->franchise);

    }


}
