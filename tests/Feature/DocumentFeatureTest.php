<?php

namespace Tests\Feature;

use App\Document;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class DocumentFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanListDocumentByLead()
    {

        $lead = factory(Lead::class)->create();

        factory(Document::class, 5)->create(['lead_id' => $lead->id]);

        $this->authenticateStaffUser();

        $response = $this->get('api/leads/'. $lead->id . '/documents');

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }
}
