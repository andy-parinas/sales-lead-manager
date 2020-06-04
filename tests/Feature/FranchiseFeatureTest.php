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
        $parent = factory(Franchise::class)->create();
        $franchise = factory(Franchise::class)->create();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::HEAD_OFFICE]),
            ['*']
        );

        $updates = [
            'franchise_number' => '1234',
            'name' => 'updated',
            'description' => 'description',
            'parent_id' => ''
        ];

        $this->put('api/franchises/' . $franchise->id, $updates)
            ->assertStatus(Response::HTTP_OK);

        $franchise->refresh();

        $this->assertEquals($updates['franchise_number'], $franchise->franchise_number);


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

    public function testCanShowRelatedFranchiseParentFranchise()
    {
        $this->withoutExceptionHandling();

        $parent = factory(Franchise::class)->create();
        factory(Franchise::class, 5)->create(['parent_id' => $parent->id]);

        //Haystack
        factory(Franchise::class, 3)->create();


        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/franchises/'. $parent->id . '/related');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonCount(6, 'data');

    }


    public function testCanShowRelatedFranchiseChildFranchise()
    {
        $this->withoutExceptionHandling();

        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);
        factory(Franchise::class, 4)->create(['parent_id' => $parent->id]);

        //Haystack
        factory(Franchise::class, 3)->create();


        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/franchises/'. $child->id . '/related');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(6, 'data');

    }

    public function testCanListAllParentFranchise()
    {
        factory(Franchise::class, 5)->create()->each(function ($franchise){
            factory(Franchise::class)->create(['parent_id' => $franchise->id]);
        });

        $this->authenticateHeadOfficeUser();

        $this->get('api/franchises/parents')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');



    }

    public function testCanListFranchisesWithoutSizeParams()
    {
        $this->withoutExceptionHandling();

        factory(Franchise::class, 15)->create();

        $this->authenticateHeadOfficeUser();

        $response = $this->get("api/franchises?sort=franchise_number&direction=asc&");

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(15, 'data');
    }



}
