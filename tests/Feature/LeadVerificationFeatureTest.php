<?php

namespace Tests\Feature;

use App\Lead;
use App\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadVerificationFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanShowLeadVerification()
    {

        $lead = factory(Lead::class)->create();

        factory(Verification::class)->create([
            'lead_id' => $lead->id
        ]);

        $this->authenticateHeadOfficeUser();

        $this->get("api/leads/{$lead->id}/verifications")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1);


    }

    public function testCanCreateLeadVerification()
    {

        $lead = factory(Lead::class)->create();

        $verificationData = factory(Verification::class)->raw();

        $this->authenticateHeadOfficeUser();

        $this->post("api/leads/{$lead->id}/verifications", $verificationData)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, Verification::all());
    }


}
