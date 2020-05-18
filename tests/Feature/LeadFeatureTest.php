<?php

namespace Tests\Feature;

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanListAllLeadsByHeadOfficeUsers()
    {

        // Each Lead here will have its own Franchise.
        // this should be listed by HeadOffice User without referencing to a franchise
        factory(Lead::class, 10)->create();

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $this->get('api/leads/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');
    }


    public function testCanListLeadsByUser()
    {

        $this->withoutExceptionHandling();

        $user = $this->createStaffUser();
        $franchise1 = factory(Franchise::class)->create();
        $franchise2 = factory(Franchise::class)->create();

        $user->franchises()->attach([$franchise1->id, $franchise2->id]);


        factory(Lead::class,5)->create(['franchise_id'=>$franchise1->id]);
        factory(Lead::class,5)->create(['franchise_id'=>$franchise2->id]);


        //Haystack
        factory(Lead::class,5)->create();

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/leads/');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }

    public function testCanListLeadsByLeadNumber(){

        $this->withoutExceptionHandling();

        $user = $this->createStaffUser();
        $franchise1 = factory(Franchise::class)->create();
        $franchise2 = factory(Franchise::class)->create();

        $user->franchises()->attach([$franchise1->id, $franchise2->id]);

        for ($i =1; $i<= 10; $i++){
            factory(Lead::class)->create(['lead_number' => $i + 20, 'franchise_id'=>$franchise1->id]);
            factory(Lead::class)->create(['lead_number' => $i + 10, 'franchise_id'=>$franchise2->id]);
        }



        //Haystack
        factory(Lead::class,5)->create();

        Sanctum::actingAs(
            $user,
            ['*']
        );

//        $response = $this->get('api/leads?sort=leadNumber&direction=asc');
        $response = $this->get('api/leads?size=10&sort=leadNumber&direction=asc&page=1');


        $result = json_decode($response->content());

        //dd($result);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');
    }

//    public function testCanNotListAllLeadsNyNonHeadOfficeUsers()
//    {
//
//        factory(Lead::class, 10)->create();
//
//        Sanctum::actingAs(
//            $this->createFranchiseAdminUser(),
//            ['*']
//        );
//
//        $this->get('api/leads/')
//            ->assertStatus(Response::HTTP_FORBIDDEN);
//
//
//        Sanctum::actingAs(
//            $this->createStaffUser(),
//            ['*']
//        );
//
//        $this->get('api/leads/')
//            ->assertStatus(Response::HTTP_FORBIDDEN);
//    }

    public function testCanShowLeadWithoutReferenceToFranchiseByHeadOfficeUsers( )
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $response = $this->get('api/leads/'. $lead->id);
        $result = json_decode($response->content())->data;

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($lead->lead_number, $result->details->leadNumber);
    }

    public function testCanNotShowLeadWithOutFranchiseReferenceByNonHeadOffice()
    {
        // $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        Sanctum::actingAs(
            $this->createStaffUser(),
            ['*']
        );

        $this->get('api/leads/'. $lead->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        Sanctum::actingAs(
            $this->createFranchiseAdminUser(),
            ['*']
        );

        $this->get('api/leads/'. $lead->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }


    public function testCanUpdateLeadByFranchiseAdmin()
    {
        $this->withoutExceptionHandling();
        /**
         * FranchiseAdmin can change the Franchise within it's assigned Franchise only
         */

        $postcode1 = factory(Postcode::class)->create();
        $franchise1 = factory(Franchise::class)->create();
        $franchise1->postcodes()->attach($postcode1->id);
        $contact1 = factory(SalesContact::class)->create(['postcode' => $postcode1->pcode]);

        $postcode2 = factory(Postcode::class)->create();
        $franchise2 = factory(Franchise::class)->create();
        $franchise2->postcodes()->attach($postcode2->id);
        $contact2 = factory(SalesContact::class)->create(['postcode' => $postcode2->pcode]);

        $franchise3 = factory(Franchise::class)->create();


        $franchiseAdmin = $this->createFranchiseAdminUser();
        $franchiseAdmin->franchises()->attach([$franchise1->id, $franchise2->id]);

        $staffUser = $this->createStaffUser();
        $staffUser->franchises()->attach($franchise1);

        $lead = factory(Lead::class)->create([
           'franchise_id' => $franchise1->id,
           'sales_contact_id' => $contact2->id,
           'postcode_status' => Lead::OUTSIDE_OF_FRANCHISE
        ]);

        factory(JobType::class)->create(['lead_id' => $lead->id]);
        factory(Appointment::class)->create(['lead_id' => $lead->id]);

        $leadSource = factory(LeadSource::class)->create();


        $updates = [
            'lead_number' => '123456789',
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-04-30',
            'franchise_id' => $franchise2->id
        ];

        Sanctum::actingAs(
            $franchiseAdmin,
            ['*']
        );

        $response = $this->put('api/leads/' . $lead->id, $updates);
        $response->assertStatus(Response::HTTP_OK);

        $updatedLead = Lead::first();
        $this->assertEquals($leadSource->id, $updatedLead->lead_source_id);
        $this->assertEquals('2020-04-30', $updatedLead->lead_date);
        $this->assertEquals($franchise2->id, $updatedLead->franchise_id);
        $this->assertEquals(Lead::INSIDE_OF_FRANCHISE, $updatedLead->postcode_status);


    }


    public function testCanNotUpdateLeadByFranchiseAdminOUtsideFranchise()
    {
        /**
         * FranchiseAdmin can change the Franchise within it's assigned Franchise only
         */

        $postcode1 = factory(Postcode::class)->create();
        $franchise1 = factory(Franchise::class)->create();
        $franchise1->postcodes()->attach($postcode1->id);
        $contact1 = factory(SalesContact::class)->create(['postcode' => $postcode1->pcode]);

        $postcode2 = factory(Postcode::class)->create();
        $franchise2 = factory(Franchise::class)->create();
        $franchise2->postcodes()->attach($postcode2->id);
        $contact2 = factory(SalesContact::class)->create(['postcode' => $postcode2->pcode]);

        $franchise3 = factory(Franchise::class)->create();


        $franchiseAdmin = $this->createFranchiseAdminUser();
        $franchiseAdmin->franchises()->attach([$franchise1->id, $franchise2->id]);

        $staffUser = $this->createStaffUser();
        $staffUser->franchises()->attach($franchise1);

        $lead = factory(Lead::class)->create([
            'franchise_id' => $franchise1->id,
            'sales_contact_id' => $contact2->id,
            'postcode_status' => Lead::OUTSIDE_OF_FRANCHISE
        ]);

        factory(JobType::class)->create(['lead_id' => $lead->id]);
        factory(Appointment::class)->create(['lead_id' => $lead->id]);

        $leadSource = factory(LeadSource::class)->create();


        $updates = [
            'lead_number' => '123456789',
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-04-30',
            'franchise_id' => $franchise3->id
        ];

        Sanctum::actingAs(
            $franchiseAdmin,
            ['*']
        );

        $response = $this->put('api/leads/' . $lead->id, $updates);
        $response->assertStatus(Response::HTTP_FORBIDDEN);


    }


    public function testCanUpdateLeadByStaffUser()
    {
        $this->withoutExceptionHandling();
        /**
         * FranchiseAdmin can change the Franchise within it's assigned Franchise only
         */

        $postcode1 = factory(Postcode::class)->create();
        $franchise1 = factory(Franchise::class)->create();
        $franchise1->postcodes()->attach($postcode1->id);
        $contact1 = factory(SalesContact::class)->create(['postcode' => $postcode1->pcode]);

        $postcode2 = factory(Postcode::class)->create();
        $franchise2 = factory(Franchise::class)->create();
        $franchise2->postcodes()->attach($postcode2->id);
        $contact2 = factory(SalesContact::class)->create(['postcode' => $postcode2->pcode]);

        $staffUser = $this->createStaffUser();
        $staffUser->franchises()->attach($franchise1);

        $lead = factory(Lead::class)->create([
            'franchise_id' => $franchise1->id,
            'sales_contact_id' => $contact2->id,
            'postcode_status' => Lead::OUTSIDE_OF_FRANCHISE
        ]);

        factory(JobType::class)->create(['lead_id' => $lead->id]);
        factory(Appointment::class)->create(['lead_id' => $lead->id]);

        $leadSource = factory(LeadSource::class)->create();


        $updates = [
            'lead_number' => '123456789',
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-04-30',
            'franchise_id' => $franchise2->id
        ];

        Sanctum::actingAs(
            $staffUser,
            ['*']
        );

        $response = $this->put('api/leads/' . $lead->id, $updates);
        $response->assertStatus(Response::HTTP_OK);


        $response = $this->put('api/leads/' . $lead->id, $updates);
        $response->assertStatus(Response::HTTP_OK);

        $updatedLead = Lead::first();
        $this->assertEquals($leadSource->id, $updatedLead->lead_source_id);
        $this->assertEquals('2020-04-30', $updatedLead->lead_date);
        $this->assertEquals($franchise1->id, $updatedLead->franchise_id);
        $this->assertEquals(Lead::OUTSIDE_OF_FRANCHISE, $updatedLead->postcode_status);

    }

}
