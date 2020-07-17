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
        $data = factory(SalesContact::class)->raw(['postcode_id' => $postcode->id]);

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

        $data = factory(SalesContact::class)->raw([
            'postcode_id' => 10
        ]);

        $this->authenticateStaffUser();

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, SalesContact::all());

    }

    public function testCanListPaginatedSalesContactByAuthenticatedUSers()
    {
        $this->withoutExceptionHandling();

        factory(SalesContact::class, 30)->create();

        $this->authenticateStaffUser();


        $this->get('api/contacts?size=10')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');
    }

    public function testCanSortSalesContactByFields()
    {

        $this->authenticateStaffUser();

        collect(['first_name', 'last_name', 'pcode', 'locality', 'state'])->each(function($field){

            if($field == 'pcode' || $field == 'locality' | $field =='state'){
                $postcode1 = factory(Postcode::class)->create([$field => 'AAAAAAAAAA']);
                $postcode2 = factory(Postcode::class)->create([$field => 'ZZZZZZZZZZ']);

                $c1 = factory(SalesContact::class)->create(['postcode_id' => $postcode1->id]);
                $c2 = factory(SalesContact::class)->create(['postcode_id' => $postcode2->id]);

                $response1 =  $this->get('api/contacts?size=10&sort=' . $field . '&direction=asc');
                $result1 = json_decode($response1->content())->data;

                $response2 =  $this->get('api/contacts?size=10&sort=' . $field . '&direction=desc');
                $result2 = json_decode($response2->content())->data;

                if($field == 'pcode'){
                    $this->assertEquals('AAAAAAAAAA', $result1[0]->postcode);
                    $this->assertEquals('ZZZZZZZZZZ', $result2[0]->postcode);

                }else if ($field == 'locality') {
                    $this->assertEquals('AAAAAAAAAA', $result1[0]->suburb);
                    $this->assertEquals('ZZZZZZZZZZ', $result2[0]->suburb);
                }

                $c1->delete();
                $c2->delete();

            }else {

                $c1 = factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);
                $c2 = factory(SalesContact::class)->create([$field => 'ZZZZZZZZZZ']);

                $response =  $this->get('api/contacts?size=10&sort=' . $field . '&direction=asc');
                $result = json_decode($response->content())->data;

                $this->assertEquals('AAAAAAAAAA', $result[0]->{$this->toCamelCase($field)});


                $response =  $this->get('api/contacts?size=10&sort=' . $field . '&direction=desc');
                $result = json_decode($response->content())->data;

                $this->assertEquals('ZZZZZZZZZZ', $result[0]->{$this->toCamelCase($field)});

                $c1->delete();
                $c2->delete();

            }

        });

    }

    public function testCanSearchSalesContactByField()
    {
        $this->authenticateStaffUser();


//        collect(['first_name', 'last_name', 'pcode', 'locality', 'state'])->each(function($field){
//
//            //Needle
//            factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);
//
//            //HayStacks
//            factory(SalesContact::class, 20)->create();
//
//
//            $response =  $this->get('api/contacts?size=10&on=' . $field . '&search=AAAAAAAAAA');
//            $result = json_decode($response->content())->data;
//
//            $this->assertEquals('AAAAAAAAAA', $result[0]->{$this->toCamelCase($field)});
//
//        });

        collect(['first_name', 'last_name', 'pcode', 'locality', 'state'])->each(function($field){

            if($field == 'pcode' || $field == 'locality' | $field =='state'){
                $postcode = factory(Postcode::class)->create([$field => 'AAAAAAAAAA']);

                $c1 = factory(SalesContact::class)->create(['postcode_id' => $postcode->id]);

                $response =  $this->get('api/contacts?size=10&on=' . $field . '&search=AAAAAAAAAA');
                $result = json_decode($response->content())->data;


                if($field == 'pcode'){
                    $this->assertEquals('AAAAAAAAAA', $result[0]->postcode);

                }else if ($field == 'locality') {
                    $this->assertEquals('AAAAAAAAAA', $result[0]->suburb);
                }

                $c1->delete();

            }else {

                $c1 = factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);

                $response =  $this->get('api/contacts?size=10&sort=' . $field . '&direction=asc');
                $result = json_decode($response->content())->data;

                $this->assertEquals('AAAAAAAAAA', $result[0]->{$this->toCamelCase($field)});

                $c1->delete();

            }

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

//    public function testCanNotUpdateSalesContactPostcodeSateSuburbWhenAlreadyReferenceToLead()
//    {
//        $this->authenticateStaffUser();
//
//        $contact = factory(SalesContact::class)->create();
//        factory(Lead::class)->create(['sales_contact_id' => $contact->id]);
//
//
//        $updates = [
//            'postcode' => 'update',
//            'state' => 'update',
//            'suburb' => 'update',
//        ];
//
//
//        $this->put('api/contacts/' . $contact->id, $updates)
//            ->assertStatus(Response::HTTP_BAD_REQUEST);
//
//        $this->assertEquals($contact->postcode, SalesContact::first()->postcode);
//    }

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

        $postcode = factory(Postcode::class)->create();
        $contact = factory(SalesContact::class)->create(['postcode_id' => $postcode->id]);

        $response = $this->get('api/contacts/' . $contact->id);
        $result = json_decode($response->content())->data;



        $response->assertStatus(Response::HTTP_OK);

        $contact->refresh();

        collect(['first_name', 'last_name', 'postcode', 'suburb', 'state',
                    'contact_number', 'email', 'email2', 'customer_type', 'status'])
            ->each(function($field) use ($contact, $result, $postcode) {

                if($field == 'postcode' || $field == 'suburb' || $field == 'state'){
                    if($field == 'postcode'){
                        $this->assertEquals($postcode->pcode, $result->{$this->toCamelCase($field)});
                    }elseif ($field == 'suburb'){
                        $this->assertEquals($postcode->locality, $result->{$this->toCamelCase($field)});
                    }else {
                        $this->assertEquals($postcode->{$field}, $result->{$this->toCamelCase($field)});
                    }

                }else {
                    $this->assertEquals($contact->{$field}, $result->{$this->toCamelCase($field)});
                }
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

    public function testCanDoCombineSearch()
    {
        $this->withoutExceptionHandling();

        //Haystack
        factory(SalesContact::class, 5)->create();

        //needle
        factory(SalesContact::class)->create([
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'superman@email.com'
        ]);

        $this->authenticateStaffUser();

        $this->get('api/contacts/search?size=10&search=andy')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

        $this->get('api/contacts/search?size=10&search=parinas')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

        $this->get('api/contacts/search?size=10&search=super')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

    }
}
