<?php

namespace Tests\Feature;

use App\Postcode;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class SalesContactFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanCreateSalesContactByAuthenticatedUser()
    {

        $this->withoutExceptionHandling();
        
        $postcode = factory(Postcode::class)->create();
        $data = factory(SalesContact::class)->raw(['postcode' => $postcode->pcode]);

        $this->authenticateStaffUser();

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, SalesContact::all());

    }

    public function testCanNotCreateSalesContactByNonAuthenticatedUser()
    {
        $postcode = factory(Postcode::class)->create();
        $data = factory(SalesContact::class)->raw(['postcode' => $postcode->pcode]);

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertCount(0, SalesContact::all());
    }

    public function testCanNotCreateSalesContactWithInvalidPostcode()
    {
        
        $data = factory(SalesContact::class)->raw();

        $this->authenticateStaffUser();

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, SalesContact::all());

    }

}
