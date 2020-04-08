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
        
        $franchise = factory(Franchise::class)->create();
        $lead = factory(Lead::class)->create(['franchise_id' => $franchise->id]);
        factory(JobType::class)->create(['lead_id' => $lead->id]);
        factory(Appointment::class)->create(['lead_id' => $lead->id]);

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $responose = $this->get('api/franchises/' . $franchise->id . '/leads/' .$lead->id);

        $results = json_decode($responose->content());

        dd($results);

    }
}
