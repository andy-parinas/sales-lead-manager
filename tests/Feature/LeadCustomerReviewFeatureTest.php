<?php

namespace Tests\Feature;

use App\CustomerReview;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadCustomerReviewFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function testCanShowCustomerReview()
    {

        $lead = factory(Lead::class)->create();

        factory(CustomerReview::class)->create([
            'lead_id' => $lead->id
        ]);

        $this->authenticateHeadOfficeUser();


        $this->get("api/leads/{$lead->id}/customer-reviews")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1);


    }

}
