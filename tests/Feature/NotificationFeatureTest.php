<?php

namespace Tests\Feature;

use App\JobType;
use App\Lead;
use App\SalesStaff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanSendEmailToDesignAdvisor()
    {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);


        $salesStaff = factory(SalesStaff::class)->create([
            'email' => 'andyp@crystaltec.com.au',
            'contact_number' => '+61481791820'
        ]);


        $lead = factory(Lead::class)->create();

        $jobType = factory(JobType::class)->create([
            'lead_id' => $lead->id,
            'sales_staff_id' => $salesStaff->id
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );


        $this->post('api/job-types/' . $lead->id . '/email/' . $salesStaff->id)
            ->assertStatus(Response::HTTP_OK);

        $jobType->refresh();

        $this->assertNotNull($jobType->email_sent_to_design_advisor);

    }

    public function testCanSendSmsToDesignAdvisor()
    {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);


        $salesStaff = factory(SalesStaff::class)->create([
            'email' => 'andyp@crystaltec.com.au',
            'contact_number' => '+61481791820'
        ]);


        $lead = factory(Lead::class)->create();

        $jobType = factory(JobType::class)->create([
            'lead_id' => $lead->id,
            'sales_staff_id' => $salesStaff->id
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );


        $this->post('api/job-types/' . $lead->id . '/sms/' . $salesStaff->id)
            ->assertStatus(Response::HTTP_OK);

        $jobType->refresh();

        $this->assertNotNull($jobType->sms_sent_to_design_advisor);

    }

}
