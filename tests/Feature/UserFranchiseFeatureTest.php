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

    public function testCanNotAttachParentFranchiseToStaffUser()
    {
        $user = $this->createStaffUser();

        $parent1 = factory(Franchise::class)->create();
      
        // $user->franchises()->attach($parent1->id);

        // $c1 = factory(Franchise::class)->create(['parent_id' => $parent1->id]);

        $data = [
            'franchises' => [
                $parent1->id
            ]
        ];

        $this->authenticateHeadOfficeUser();

        $response = $this->post('api/users/' . $user->id . '/franchises', $data);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

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

}
