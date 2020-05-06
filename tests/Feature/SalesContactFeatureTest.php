<?php

namespace Tests\Feature;

use App\Lead;
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

    public function testCanListPaginatedSalesContactByAuthenticatedUSers()
    {
        factory(SalesContact::class, 30)->create();

        $this->authenticateStaffUser();


        $this->get('api/contacts')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');
    }

    public function testCanSortSalesContactByFields()
    {

        $this->authenticateStaffUser();


        collect(['first_name', 'last_name', 'postcode', 'suburb', 'state'])->each(function($field){

            $c1 = factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);
            $c2 = factory(SalesContact::class)->create([$field => 'ZZZZZZZZZZ']);

            $response =  $this->get('api/contacts?sort=' . $this->toCamelCase($field) . '&direction=asc');
            $result = json_decode($response->content())->data;

            $this->assertEquals('AAAAAAAAAA', $result[0]->{$this->toCamelCase($field)});

            $response =  $this->get('api/contacts?sort=' . $this->toCamelCase($field) . '&direction=desc');
            $result = json_decode($response->content())->data;

            $this->assertEquals('ZZZZZZZZZZ', $result[0]->{$this->toCamelCase($field)});

            //Delete all the record to start fresh
            $c1->delete();
            $c2->delete();


        });

    }

    public function testCanSearchSalesContactByField()
    {
        $this->authenticateStaffUser();


        collect(['first_name', 'last_name', 'postcode', 'suburb', 'state'])->each(function($field){

            //Needle
            factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);

            //HayStacks
            factory(SalesContact::class, 20)->create();


            $response =  $this->get('api/contacts?on=' . $field . '&search=AAAAAAAAAA');
            $result = json_decode($response->content())->data;

            $this->assertEquals('AAAAAAAAAA', $result[0]->{$this->toCamelCase($field)});

        });
    }


    public function testCanUpdateSalesContactInformation()
    {

        $this->authenticateStaffUser();

        $contact = factory(SalesContact::class)->create();


        $updates = [
            'title' => 'update',
            'first_name' => 'update',
            'last_name' => 'update',
            'email' => 'update@email.com',
            'contact_number' => 'update',
            'street1' => 'update',
            'street2' => 'update'
        ];


        $this->put('api/contacts/' . $contact->id, $updates)
            ->assertStatus(Response::HTTP_OK);


        $contact->refresh();

        $this->assertEquals('update', $contact->first_name );


    }

    public function testCanNotUpdateSalesContactPostcodeSateSuburbWhenAlreadyReferenceToLead()
    {
        $this->authenticateStaffUser();

        $contact = factory(SalesContact::class)->create();
        factory(Lead::class)->create(['sales_contact_id' => $contact->id]);


        $updates = [
            'postcode' => 'update',
            'state' => 'update',
            'suburb' => 'update',
        ];


        $this->put('api/contacts/' . $contact->id, $updates)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertEquals($contact->postcode, SalesContact::first()->postcode);
    }

    public function testCanUpdateContactStatusByStaffUser(){

        $this->authenticateStaffUser();

        $contact = factory(SalesContact::class)->create(['status' => 'active']);

        $this->put('api/contacts/' . $contact->id, ['status' => 'archived'])
            ->assertStatus(Response::HTTP_OK);


        $contact->refresh();

        $this->assertEquals('archived', $contact->status );

    }

    public function testCanShowSalesContact()
    {
        $this->authenticateStaffUser();

        $contact = factory(SalesContact::class)->create();

        $response = $this->get('api/contacts/' . $contact->id);
        $result = json_decode($response->content())->data;

        $response->assertStatus(Response::HTTP_OK);

        $contact->refresh();

        collect(['first_name', 'last_name', 'postcode', 'suburb', 'state',
                    'contact_number', 'email', 'email2', 'customer_type', 'status'])
            ->each(function($field) use ($contact, $result) {
                $this->assertEquals($contact->{$field}, $result->{$this->toCamelCase($field)});
        });

    }

    public function testCanShowTheLeadsForSalesContact()
    {
        $this->authenticateStaffUser();

        $contact = factory(SalesContact::class)->create();
        factory(Lead::class, 5)->create(['sales_contact_id' => $contact->id]);

        $response = $this->get('api/contacts/' . $contact->id);
        $result = json_decode($response->content())->data;

        $this->assertCount(5, $result->leads);
    }


    public function testCanDeleteSalesContactByHeadOffice()
    {
        $this->authenticateHeadOfficeUser();

        $contact = factory(SalesContact::class)->create();

        $this->delete('api/contacts/' . $contact->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(0, SalesContact::all());
    }


    public function testCanNotDeleteSalesContactByNonHeadOffice()
    {


        $contact = factory(SalesContact::class)->create();


        $this->authenticateFranchiseAdmin();
        $this->delete('api/contacts/' . $contact->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, SalesContact::all());

        $this->authenticateStaffUser();
        $this->delete('api/contacts/' . $contact->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, SalesContact::all());

    }

    public function testCanNotDeleteSalesContactWithRelatedLead()
    {
        $this->authenticateHeadOfficeUser();

        $contact = factory(SalesContact::class)->create();
        factory(Lead::class, 5)->create(['sales_contact_id' => $contact->id]);

        $this->delete('api/contacts/' . $contact->id)
        ->assertStatus(Response::HTTP_CONFLICT);
    }

    /**
     * Some of the torubleshooting test
     */

    public function testStatusIsIncludedWhenSalesContactIsCreated()
    {
        $this->authenticateStaffUser();


        $postcode = factory(Postcode::class)->create();
        $data = factory(SalesContact::class)->raw(['postcode' => $postcode->pcode, 'status' => 'active']);

        $this->authenticateStaffUser();

        $response = $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $results = json_decode($response->content())->data;

//        dd($results);
    }
}
