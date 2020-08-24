<?php

namespace Tests\Feature;

use App\Construction;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class ConstructionFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanShowLeadConstruction()
    {


        $lead = factory(Lead::class)->create();

        $construction = factory(Construction::class)->create([
            'lead_id' => $lead->id
        ]);

        $this->authenticateHeadOfficeUser();

        $response = $this->get('/api/leads/' . $lead->id . '/constructions');


        $response->assertStatus(Response::HTTP_OK);

    }

    public function testCanShowNoContentLeadWithoutConstruction()
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $this->authenticateHeadOfficeUser();

        $response = $this->get('/api/leads/' . $lead->id . '/constructions');


        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

}
