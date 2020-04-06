<?php

namespace Tests\Feature;

use App\Franchise;
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

class FranchiseLeadFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanListLeadsUnderUsersFranchise()
    {
        $this->withoutExceptionHandling();

        $franchise = factory(Franchise::class)->create();
        factory(Lead::class, 10)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/leads')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }

    public function testCanNotListLeadsOutsideUsersFranchise()
    {

        $franchise = factory(Franchise::class)->create();
        factory(Lead::class, 10)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $this->createStaffUser(),
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/leads')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }


    public function testCanListAllLeadByHeadOffice()
    {
        
        $franchise = factory(Franchise::class)->create();
        factory(Lead::class, 10)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/leads')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }

    public function testCanShowLeadUnderUsersFranchise()
    {
        
        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        $franchiseAdmin = $this->createFranchiseAdminUser();
        $franchiseAdmin->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads/' . $lead->id);
        $result = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($lead->number, $result->data->number);

        Sanctum::actingAs(
            $franchiseAdmin,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads/' . $lead->id);
        $result = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($lead->number, $result->data->number);

    }

    public function testCanNotShowLeadOutsideUsersFranchise()
    {
         
        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $franchiseAdmin = $this->createFranchiseAdminUser();


        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads/' . $lead->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        Sanctum::actingAs(
            $franchiseAdmin,
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads/' . $lead->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanShowAnyLeadByHeadOfficeUsers()
    {
        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);


        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads/' . $lead->id);
        $result = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($lead->number, $result->data->number);

      
    }


    public function testCanCreateLeadUnderStaffUsersFranchise()
    {

        $this->withoutExceptionHandling();
        
        $postcode = factory(Postcode::class)->create();
        $franchise = factory(Franchise::class)->create();
        $franchise->postcodes()->attach($postcode->id);


        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        $salesContact = factory(SalesContact::class)->create(['postcode' => $postcode->pcode]);
        $leadSource = factory(LeadSource::class)->create();

        $leadData = [
            'number' => '1234567890',
            'sales_contact_id' => $salesContact->id,
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-03-30'
        ];

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/franchises/' . $franchise->id . '/leads', $leadData)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, Lead::all());       

    }

    public function testCanNotCreateLeadOutsideStaffUsersFranchise()
    {
        
        
        // $this->withoutExceptionHandling();
        
        $franchise = factory(Franchise::class)->create();

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        $salesContact = factory(SalesContact::class)->create();
        $leadSource = factory(LeadSource::class)->create();

        $leadData = [
            'number' => '1234567890',
            'sales_contact_id' => $salesContact->id,
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-03-30'
        ];

        Sanctum::actingAs(
            $this->createStaffUser(), //Create a new staff user acting as a User
            ['*']
        );

        $this->post('api/franchises/' . $franchise->id . '/leads', $leadData)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, Lead::all());       


    }

    public function testCreatedLeadHasPostcodeStatusInside()
    {
       
        $postcode = factory(Postcode::class)->create();
        $franchise = factory(Franchise::class)->create();
        $franchise->postcodes()->attach($postcode->id);


        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        $salesContact = factory(SalesContact::class)->create(['postcode' => $postcode->pcode]);
        $leadSource = factory(LeadSource::class)->create();

        $leadData = [
            'number' => '1234567890',
            'sales_contact_id' => $salesContact->id,
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-03-30'
        ];

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/franchises/' . $franchise->id . '/leads', $leadData);
        $this->assertEquals(Lead::INSIDE_OF_FRANCHISE, Lead::first()->postcode_status);
        

    }

    public function testCreatedLeadHasPostcodeStatusOutside()
    {
        $postcode = factory(Postcode::class)->create();
        $franchise = factory(Franchise::class)->create();
        $franchise->postcodes()->attach($postcode->id);


        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        $salesContact = factory(SalesContact::class)->create();
        $leadSource = factory(LeadSource::class)->create();

        $leadData = [
            'number' => '1234567890',
            'sales_contact_id' => $salesContact->id,
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-03-30'
        ];

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/franchises/' . $franchise->id . '/leads', $leadData);
        $this->assertEquals(Lead::OUTSIDE_OF_FRANCHISE, Lead::first()->postcode_status);
    }


    public function testCanUpdateLeadDataExceptNumberByStaffUsers()
    {

        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        $leadSource = factory(LeadSource::class)->create();

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $updates = [
            'lead_source_id' => $leadSource->id,
            'lead_date' => '2020-04-30'
        ];

        $this->put('api/franchises/' . $franchise->id . '/leads/' . $lead->id, $updates)
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($leadSource->id, Lead::first()->lead_source_id);
        $this->assertEquals('2020-04-30', Lead::first()->lead_date);
        
    }

    public function testCanNotUpdateLeadDataByUserOutsideFranchise()
    {
        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $this->createStaffUser(),
            ['*']
        );

        $updates = [
            'lead_date' => '2020-04-30'
        ];

        $this->put('api/franchises/' . $franchise->id . '/leads/' . $lead->id, $updates)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals($lead->lead_date, Lead::first()->lead_date);
    }


    public function testCanChangeFranchiseByAdminUnderTheirAssignedFranchise()
    {
        
        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        //Will Transfer the Lead to this Franchise
        $franchise2= factory(Franchise::class)->create();

        $user = $this->createFranchiseAdminUser();
        $user->franchises()->attach([$franchise->id, $franchise2->id]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $updates = [
            'franchise_id' => $franchise2->id
        ];

        $this->put('api/franchises/' . $franchise->id . '/leads/' . $lead->id, $updates)
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($franchise2->id, Lead::first()->franchise_id);
    }

    public function testCanNotChangeLeadFranchiseByStaffUsers()
    {

        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);

        $franchise2= factory(Franchise::class)->create();

        $user = $this->createStaffUser();
        $user->franchises()->attach([$franchise->id, $franchise2->id]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $updates = [
            'franchise_id' => $franchise2->id
        ];

        $this->put('api/franchises/' . $franchise->id . '/leads/' . $lead->id, $updates)
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals($franchise->id, Lead::first()->franchise_id);
    }

}
