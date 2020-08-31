<?php

namespace Tests\Feature;

use App\BuildingAuthority;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class BuildingAuthorityFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanShowBuildingAuthority()
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        factory(BuildingAuthority::class)->create([
            'lead_id' => $lead->id
        ]);

        $this->authenticateHeadOfficeUser();

        $this->get('api/leads/' . $lead->id . '/authorities')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1);

    }

    public function testCanCreateBuildingAuthority()
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $data = factory(BuildingAuthority::class)->raw(['lead_id' => $lead->id]); // so as not to create another lead


        $this->authenticateHeadOfficeUser();

        $this->post('api/leads/' . $lead->id . '/authorities', $data)
            ->assertStatus(Response::HTTP_CREATED);


    }

}
