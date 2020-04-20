<?php

namespace Tests\Feature;

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
}
