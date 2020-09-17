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


    public function testCanCreateCustomerReview()
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $data = factory(CustomerReview::class)->raw();


        $this->authenticateHeadOfficeUser();


        $this->post("api/leads/{$lead->id}/customer-reviews", $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(1);

    }

}
