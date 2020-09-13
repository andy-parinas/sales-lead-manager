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

        $this->get("api/leads/{$lead->id}/verifications")
            ->assertStatus(Response::HTTP_OK);


    }


}
