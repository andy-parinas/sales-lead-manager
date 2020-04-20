<?php

namespace Tests\Feature;

use App\LeadSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadSourceFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;

    public function testCanCreateLeadSourceByHeadOffice()
    {
        $data = [
            'name' => 'Test Lead Source'
        ];


        $this->authenticateHeadOfficeUser();

        $this->post('api/lead-sources', $data)
            ->assertStatus(Response::HTTP_CREATED);

    }

    public function testCanNotCreateLeadSourceByNonHeadOffice()
    {
        $data = [
            'name' => 'Test Lead Source'
        ];


        $this->authenticateFranchiseAdmin();
        $this->post('api/lead-sources', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->authenticateStaffUser();
        $this->post('api/lead-sources', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanListLeadSourceByAuthenticatedUsers()
    {
        factory(LeadSource::class, 5)->create();

        $this->authenticateStaffUser();

        $this->get('api/lead-sources')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');
    }

    public function testCanNotListLeadSourceByNonAuthenticatedUser()
    {
        factory(LeadSource::class, 5)->create();

        $this->get('api/lead-sources')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCanUpdateLeadSourceByHeadOffice()
    {
        $source = factory(LeadSource::class)->create();

        $this->authenticateHeadOfficeUser();

        $this->put('api/lead-sources/' . $source->id, ['name' => 'updated'] )
            ->assertStatus(Response::HTTP_OK);

        $source->refresh();

        $this->assertEquals('updated', $source->name);
    }

    public function testCanNotUpdateLeadSourceByNonHeadOffice()
    {
        $source = factory(LeadSource::class)->create();


        $this->authenticateFranchiseAdmin();

        $this->put('api/lead-sources/' . $source->id, ['name' => 'updated'] )
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
