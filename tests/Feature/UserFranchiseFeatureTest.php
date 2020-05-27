<?php

namespace Tests\Feature;

use App\Franchise;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class UserFranchiseFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;

    public function testCanAttachFranchiseToUserByHeadOffice()
    {

        $this->withoutExceptionHandling();

        $user = $this->createFranchiseAdminUser();

        $franchise = factory(Franchise::class)->create();

        $data = [
            'franchises' => [$franchise->id]
        ];

        $this->authenticateHeadOfficeUser();

        $this->post('api/users/' . $user->id . '/franchises', $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(1);
    }

    public function testCanNotAttachMultipleParentFranchiseToFranchiseAdmin()
    {
        $user = $this->createFranchiseAdminUser();

        $parent1 = factory(Franchise::class)->create();
        $parent2 = factory(Franchise::class)->create();

        $data = [
            'franchises' => [$parent1->id, $parent2->id]
        ];

        $this->authenticateHeadOfficeUser();

        $this->post('api/users/' . $user->id . '/franchises', $data)
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCanAttachRelatedFranchiseOnly()
    {
        $this->withoutExceptionHandling();
        $user = $this->createFranchiseAdminUser();

        $parent1 = factory(Franchise::class)->create();
        $parent2 = factory(Franchise::class)->create();

        // $user->franchises()->attach($parent1->id);

        $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c2 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c3 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c4 = factory(Franchise::class)->create(['parent_id' => $parent2->id]);

        $data = [
            'franchises' => [
                $c1->id,
                $c2->id,
                $c3->id,
                $parent1->id,
                $c4->id // This will be excluded in the attachment of the Franchise
            ]
        ];

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);

        $response->assertStatus(Response::HTTP_CREATED)
                ->assertJsonCount(4, 'data');
    }

    public function testCanNotAttachFranchiseToFreshAdminWithoutIncludingParent()
    {
        $user = $this->createFranchiseAdminUser();

        $parent1 = factory(Franchise::class)->create();

        // $user->franchises()->attach($parent1->id);

        $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c2 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c3 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);


        $data = [
            'franchises' => [
                $c1->id,
                $c2->id,
                $c3->id,
            ]
        ];

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCanOnlyAttachChildrenFranchiseToExisitingAdminWithFranchise()
    {
        $user = $this->createFranchiseAdminUser();

        $parent1 = factory(Franchise::class)->create();
        $parent2 = factory(Franchise::class)->create();

        $user->franchises()->attach($parent1->id);
        factory(Franchise::class,5)->create(['parent_id' => $parent1->id])->each(function($franchise) use ($user){
            $user->franchises()->attach($franchise->id);
        });
        // $user->franchises()->attach($parent1->id);

        $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c2 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c3 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
        $c4 = factory(Franchise::class)->create(['parent_id' => $parent2->id]);
        $c5 = factory(Franchise::class)->create(['parent_id' => $parent2->id]);

        $data = [
            'franchises' => [
                $c1->id,
                $c2->id,
                $c3->id,
                $c4->id, // Will not be attached since parent is different that what was previously set in the user
                $c5->id, // Will not be attached
            ]
        ];

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);

        // dd(json_decode($response->content()));


        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(9, 'data');

    }


    public function testCanOnlyAttachOneFranchiseToStaffUser()
    {
        $user = $this->createStaffUser();

        $parent1 = factory(Franchise::class)->create();

        // $user->franchises()->attach($parent1->id);

        $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);

        $data = [
            'franchises' => [
                $c1->id
            ]
        ];

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(1, 'data');
    }

//    public function testCanNotAttachParentFranchiseToStaffUser()
//    {
//        $user = $this->createStaffUser();
//
//        $parent1 = factory(Franchise::class)->create();
//
//        // $user->franchises()->attach($parent1->id);
//
//        // $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);
//
//        $data = [
//            'franchises' => [
//                $parent1->id
//            ]
//        ];
//
//        $this->authenticateHeadOfficeUser();
//
//        $response = $this->post('api/users/' . $user->id . '/franchises', $data);
//
//        $response->assertStatus(Response::HTTP_BAD_REQUEST);
//    }

    public function testCanNotAttachFranchiseByNonHeadOffice()
    {
        $user = $this->createStaffUser();

        $parent1 = factory(Franchise::class)->create();

        // $user->franchises()->attach($parent1->id);

        $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);

        $data = [
            'franchises' => [
                $c1->id
            ]
        ];

        $this->authenticateFranchiseAdmin();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->authenticateStaffUser();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }



    public function testCanDetachFranchiseFromUserByHeadOffice()
    {
        $user = factory(User::class)->create();
        $franchise = factory(Franchise::class)->create();

        $user->franchises()->attach($franchise->id);

        $this->authenticateHeadOfficeUser();

        $this->delete('api/users/' . $user->id . '/franchises/' . $franchise->id)
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertCount(0, $user->franchises);

    }

    public function testCanNotDetachFranchiseFromUserByNonHeadOffice()
    {
        $user = factory(User::class)->create();
        $franchise = factory(Franchise::class)->create();

        $user->franchises()->attach($franchise->id);

        $this->authenticateFranchiseAdmin();

        $this->delete('api/users/' . $user->id . '/franchises/' . $franchise->id)
        ->assertStatus(Response::HTTP_FORBIDDEN);
        $user->refresh();
        $this->assertCount(1, $user->franchises);

        $this->authenticateStaffUser();

        $this->delete('api/users/' . $user->id . '/franchises/' . $franchise->id)
        ->assertStatus(Response::HTTP_FORBIDDEN);
        $user->refresh();
        $this->assertCount(1, $user->franchises);

    }

    public function testCanNotDetachParentFranchiseWithChildren()
    {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $franchise = factory(Franchise::class)->create();
        $c1 = factory(Franchise::class)->create(['parent_id' => $franchise->id]);
        $c2 = factory(Franchise::class)->create(['parent_id' => $franchise->id]);

        $user->franchises()->attach([$franchise->id, $c1->id, $c2->id]);

        $this->authenticateHeadOfficeUser();

        $this->delete('api/users/' . $user->id . '/franchises/' . $franchise->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->refresh();

        $this->assertCount(3, $user->franchises);

    }


    public function testCanDetachChildrenFranchiseFromUserByHeadOffice()
    {

        $user = factory(User::class)->create();
        $franchise = factory(Franchise::class)->create();
        $c1 = factory(Franchise::class)->create(['parent_id' => $franchise->id]);
        $c2 = factory(Franchise::class)->create(['parent_id' => $franchise->id]);

        $user->franchises()->attach([$franchise->id, $c1->id, $c2->id]);

        $this->authenticateHeadOfficeUser();

        $this->delete('api/users/' . $user->id . '/franchises/' . $c1->id)
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertCount(2, $user->franchises);
    }

    public function testCanListUsersFranchiseByHeadOffice()
    {
        $user = $this->createFranchiseAdminUser();

        factory(Franchise::class, 15)->create()->each(function ($franchise) use ($user) {
            $franchise->users()->attach($user->id);
        });


        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/users/' . $user->id . '/franchises');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');


    }


    public function testCanAttachChildFranchiseToStaffUser()
    {

        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);
        $user = $this->createStaffUser();


        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $child->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, $user->franchises);


    }

    public function testCanNotAttachMoreThanOneChildFranchiseToStaffUser()
    {


        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);
        $child2 = factory(Franchise::class)->create(['parent_id' => $parent->id]);
        $user = $this->createStaffUser();
        $user->franchises()->attach($child->id);


        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $child2->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(1, $user->franchises);
    }

    public function testCanNotAttachParentFranchiseToStaffUser()
    {


        $parent = factory(Franchise::class)->create();

        $user = $this->createStaffUser();



        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $parent->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, $user->franchises);
    }


    public function testCanAttachParentFranchiseToFranchiseAdmin()
    {
        $parent = factory(Franchise::class)->create();
        $user = $this->createFranchiseAdminUser();

        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $parent->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, $user->franchises);
    }

    public function testCanNotAttachMoreThanOneParentFranchise()
    {
        $parent = factory(Franchise::class)->create();
        $user = $this->createFranchiseAdminUser();
        $user->franchises()->attach($parent->id);
        $parent2 = factory(Franchise::class)->create();


        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $parent2->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(1, $user->franchises);
    }

    public function testCanAttachChildFranchiseBelongingToParent()
    {
        $this->withoutExceptionHandling();

        $parent = factory(Franchise::class)->create();
        $user = $this->createFranchiseAdminUser();
        $user->franchises()->attach($parent->id);
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);


        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $child->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(2, $user->franchises);
    }

    public function testCanNotAttachChildFranchiseDoesNotBelongToParent()
    {

        $parent = factory(Franchise::class)->create();
        $parent2 = factory(Franchise::class)->create();
        $user = $this->createFranchiseAdminUser();
        $user->franchises()->attach($parent->id);
        $child = factory(Franchise::class)->create(['parent_id' => $parent2->id]);


        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $child->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(1, $user->franchises);
    }

    public function testCanNotAttachChildFranchiseWithoutParenFranchise()
    {
        $parent = factory(Franchise::class)->create();
        $child = factory(Franchise::class)->create(['parent_id' => $parent->id]);
        $user = $this->createFranchiseAdminUser();


        $this->authenticateHeadOfficeUser();

        $this->post('api/users/'. $user->id . '/franchises/' . $child->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, $user->franchises);
    }



}
