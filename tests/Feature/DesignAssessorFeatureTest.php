<?php

namespace Tests\Feature;

use App\DesignAssessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class DesignAssessorFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanCreateDesignAssessorByHeadOffice()
    {

        $this->authenticateHeadOfficeUser();

        $data = factory(DesignAssessor::class)->raw();


        $this->post('api/design-assessors', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, DesignAssessor::all());

    }

    public function testCanNotCreateDesignAssessorByNonHeadOffice()
    {


        $data = factory(DesignAssessor::class)->raw();


        $this->authenticateStaffUser();
        $this->post('api/design-assessors', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->authenticateFranchiseAdmin();
        $this->post('api/design-assessors', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, DesignAssessor::all());
    }

    public function testCanSearchDesignAssessorByAuthenticatedUsers()
    {
        $this->withoutExceptionHandling();

        $this->authenticateStaffUser();
        //Haystack
        factory(DesignAssessor::class, 5)->create();

        //Needle
        factory(DesignAssessor::class)->create([
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'superman@email.com'
        ]);


        //Search for FirstName
        $this->get('api/design-assessors?search=andy')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');


        $this->get('api/design-assessors?search=parinas')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

        $this->get('api/design-assessors?search=superman')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

    }

}
