<?php

namespace Tests\Feature;

use App\Franchise;
use App\Postcode;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class FranchisePostcodeFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;

    public function testCanListPostcodeFromAnyFranchiseByHeadOffice()
    {

        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        // Setting the Franchise to belong to Staff User
        // Head Office User should be able read the Postcode for this Franchise
        $user = factory(User::class)->create(['user_type' => User::STAFF_USER ]);
        $franchise = factory(Franchise::class)->create();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        factory(Postcode::class, 5)->create()->each(function($postcode) use ($franchise){
            $franchise->postcodes()->attach($postcode->id);
        });

        $this->get('api/franchises/' . $franchise->id . '/postcodes/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }

    public function testCanOnlyListPostCodeFromFranchiseByNonHeadOffice()
    {

        $user = factory(User::class)->create(['user_type' => User::STAFF_USER ]);
        $franchise = factory(Franchise::class)->create();
        $user->franchises()->attach($franchise->id);

        factory(Postcode::class, 5)->create()->each(function($postcode) use ($franchise){
            $franchise->postcodes()->attach($postcode->id);
        });

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/postcodes/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }

    public function testCanNotListPostCodeOutsideFranchiseByNonHeadOffice()
    {

        $user = factory(User::class)->create(['username' => 'testuesr123',  'user_type' => User::STAFF_USER ]);
        $franchise = factory(Franchise::class)->create();
        $user->franchises()->attach($franchise->id);

        factory(Postcode::class, 5)->create()->each(function($postcode) use ($franchise){
            $franchise->postcodes()->attach($postcode->id);
        });

        $userOutside = factory(User::class)->create(['user_type' => User::STAFF_USER ]);


        Sanctum::actingAs(
            $userOutside,
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/postcodes/')
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }

    public function testCanAttachPostcodeToFranchiseByHeadOffice()
    {

        $this->withoutExceptionHandling();

        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);
        $parent = factory(Franchise::class)->create();

        $franchise = factory(Franchise::class)->create(['parent_id' => $parent->id]);

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $data = ['postcodes' => [$p1->id, $p2->id, $p3->id]];


        $this->post('api/franchises/' . $franchise->id . '/postcodes/', $data)
            ->assertStatus(Response::HTTP_CREATED);

//        dd($franchise->postcodes);
        $this->assertCount(3, $franchise->postcodes);

    }


    public function testCanNotAttachPostcodeAlreadyAttached()
    {

//        $this->withoutExceptionHandling();

        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);
        $parent = factory(Franchise::class)->create();

        $franchise = factory(Franchise::class)->create(['parent_id' => $parent->id]);

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        $franchise->postcodes()->attach([$p1->id, $p2->id, $p3->id]);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $data = ['postcodes' => [$p1->id, $p2->id, $p3->id]];


        $this->post('api/franchises/' . $franchise->id . '/postcodes/', $data)
            ->assertStatus(409);

        $this->assertCount(3, $franchise->postcodes);

    }




    public function testCanNotAttachPostCodeToFranchiseNonHeadOffice()
    {


       $user = factory(User::class)->create(['user_type' => User::STAFF_USER ]);
       $franchise = factory(Franchise::class)->create();
       $user->franchises()->attach($franchise->id);

       $p1 = factory(Postcode::class)->create();
       $p2 = factory(Postcode::class)->create();
       $p3 = factory(Postcode::class)->create();

       Sanctum::actingAs(
           $user,
           ['*']
       );

       $data = ['postcodes' => [$p1->id, $p2->id, $p3->id]];

       $this->post('api/franchises/' . $franchise->id . '/postcodes/', $data)
           ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, Franchise::first()->postcodes);
    }

    public function testCanDetachPostcodesFromFranchiseByHeadOffice()
    {
        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        $franchise = factory(Franchise::class)->create();

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        $franchise->postcodes()->attach([$p1->id, $p2->id, $p3->id]);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $this->delete('api/franchises/' . $franchise->id . '/postcodes/' . $p1->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(2, Franchise::first()->postcodes);

    }

    public function testCanNotDetachPostcodeFromFranchiseByNonHeadOffice()
    {
        $user = factory(User::class)->create(['user_type' => User::STAFF_USER]);
        $franchise = factory(Franchise::class)->create();

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        $franchise->postcodes()->attach([$p1->id, $p2->id, $p3->id]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->delete('api/franchises/' . $franchise->id . '/postcodes/' . $p1->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(3, Franchise::first()->postcodes);
    }

    public function testCanNotDetachPostCodeWhenAssociatedToAChildFranchise()
    {

        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        $parent->postcodes()->attach([$p1->id, $p2->id, $p3->id]);
        $child->postcodes()->attach([$p1->id, $p2->id]);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $this->delete('api/franchises/' . $parent->id . '/postcodes/' . $p1->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(3, Franchise::first()->postcodes);
    }

    public function testCanDetachPostcodeFromChildFranchise()
    {
        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        $parent->postcodes()->attach([$p1->id, $p2->id, $p3->id]);
        $child->postcodes()->attach([$p1->id, $p2->id]);

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $this->delete('api/franchises/' . $child->id . '/postcodes/' . $p1->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, Franchise::find($child->id)->postcodes);
    }

    public function testCanAttachPostcodeInChildFranchiseIfPostcodeInParent()
    {
        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();

        // Out of bounds postcodes. Not in the parent postcode
        // This will not be included in the franchise postcode
        $p4 = factory(Postcode::class)->create();
        $p5 = factory(Postcode::class)->create();

        $parent->postcodes()->attach([$p1->id, $p2->id, $p3->id]);

        $data = ['postcodes' => [$p1->id, $p2->id, $p4->id, $p5->id]];
        // $data = ['postcodes' => [$p4->id, $p5->id]];


        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $this->post('api/franchises/' . $child->id . '/postcodes/', $data)
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonCount(2, 'data');
        $this->assertCount(2, Franchise::find($child->id)->postcodes);

    }

    public function testWillNotAttachPostcodeInChildIfOutsideParentPostcode()
    {
        $headOffice = factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);

        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);

        $p1 = factory(Postcode::class)->create();
        $p2 = factory(Postcode::class)->create();
        $p3 = factory(Postcode::class)->create();
        $p4 = factory(Postcode::class)->create();
        $p5 = factory(Postcode::class)->create();

        $parent->postcodes()->attach([$p1->id, $p2->id, $p3->id]);

        $data = ['postcodes' => [$p4->id, $p5->id]];

        Sanctum::actingAs(
            $headOffice,
            ['*']
        );

        $this->post('api/franchises/' . $child->id . '/postcodes/', $data)
        ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(0, Franchise::find($child->id)->postcodes);

    }


    public function testCanAttachPostcodeToParentFranchise()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();


        $this->authenticateHeadOfficeUser();


        $response = $this->post('api/franchises/'. $franchise->id . '/postcodes/'. $postcode->id . '/attach');


        $response->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, $franchise->postcodes);


    }


    public function testCanNotAttachAlreadyAttachedPostcode()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();

        $franchise->postcodes()->attach($postcode->id);


        $this->authenticateHeadOfficeUser();


        $response = $this->post('api/franchises/'. $franchise->id . '/postcodes/'. $postcode->id . '/attach');


        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(1, $franchise->postcodes);


    }


    public function testCanAttachPostcodeToChildFranchise()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();
        $franchise->postcodes()->attach($postcode->id);

        $child = factory(Franchise::class)->create(['parent_id' => $franchise->id]);

        $this->authenticateHeadOfficeUser();


        $response = $this->post('api/franchises/'. $child->id . '/postcodes/'. $postcode->id . '/attach');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, $child->postcodes);


    }

    public function testCanNotAttachPostcodeToChildIfNotInParent()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();
        //$franchise->postcodes()->attach($postcode->id);

        $child = factory(Franchise::class)->create(['parent_id' => $franchise->id]);

        $this->authenticateHeadOfficeUser();


        $response = $this->post('api/franchises/'. $child->id . '/postcodes/'. $postcode->id . '/attach');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, $child->postcodes);


    }

    public function testCanDetachPostcode()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();
        $franchise->postcodes()->attach($postcode->id);

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/franchises/'. $franchise->id . '/postcodes/'. $postcode->id . '/detach');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertCount(0, $franchise->postcodes);
    }

    public function testCanDetachPostcodeOfChildren()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();
        $franchise->postcodes()->attach($postcode->id);

        $child = factory(Franchise::class)->create(['parent_id' => $franchise->id]);
        $child->postcodes()->attach($postcode->id);

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/franchises/'. $child->id . '/postcodes/'. $postcode->id . '/detach');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertCount(0, $child->postcodes);
    }


    public function testCanNotDetachPostcodeThatIsNotAttach()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();


        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/franchises/'. $franchise->id . '/postcodes/'. $postcode->id . '/detach');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, $franchise->postcodes);
    }


    public function testCanNotDetachPostcodeThatIsAttachToChildren()
    {
        $franchise = factory(Franchise::class)->create();
        $postcode = factory(Postcode::class)->create();
        $franchise->postcodes()->attach($postcode->id);

        $child = factory(Franchise::class)->create(['parent_id' => $franchise->id]);
        $child->postcodes()->attach($postcode->id);

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/franchises/'. $franchise->id . '/postcodes/'. $postcode->id . '/detach');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(1, $franchise->postcodes);
    }


}
