<?php

namespace Tests\Feature;

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\TestHelper;

class LeadResourceTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function testShowLeadHaveTheFollowingProperties()
    {

        $responseStructure = [
            'data' => [
                'details' => [
                    'id',
                    'leadNumber',
                    'leadDate',
                    'postcodeStatus',
                    'franchiseNumber',
                    'leadSource',
                    'firstName',
                    'lastName',
                    'email',
                    'contactNumber',
                    'postcode',
                ],
                'jobType' => [
                    'takenBy',
                    'dateAllocated',
                    'description',
                    'productId',
                    'product',
                    'designAssessorId',
                    'designAssessor'
                ],
                'appointment' => [
                    'date',
                    'notes',
                    'quotedPrice',
                    'outcome',
                    'comments'
                ]
            ]
        ];

        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);
        factory(JobType::class)->create(['lead_id' => $lead->id]);
        factory(Appointment::class)->create(['lead_id' => $lead->id]);

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $response = $this->get('api/franchises/' . $franchise->id . '/leads/' .$lead->id);
        $response->assertJsonStructure($responseStructure);

        // $results = json_decode($response->content());

        // dd($results);

    }
}
