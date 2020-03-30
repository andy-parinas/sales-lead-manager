<?php

namespace Tests\Unit;

use App\Appointment;
use App\Branch;
use App\Document;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LeadTest extends TestCase
{
   use RefreshDatabase;

   public function testLeadBelongsToAFranchise()
   {
       $branch = factory(Franchise::class)->create();
       $lead = factory(Lead::class)->create(['franchise_id' => $branch->id]);

       $this->assertInstanceOf(Franchise::class, $lead->franchise);
       $this->assertEquals($branch->number, $lead->franchise->number);
   }

   public function testLeadBelongsToASalesContact()
   {
       $salesContact = factory(SalesContact::class)->create();
       $lead = factory(Lead::class)->create(['sales_contact_id' => $salesContact->id]);

       $this->assertInstanceOf(SalesContact::class, $lead->salesContact);
       $this->assertEquals($salesContact->first_name, $lead->salesContact->first_name);
   }

   public function testLeadBelongsToALeadSource()
   {
       $leadSource = factory(LeadSource::class)->create();
       $lead = factory(Lead::class)->create(['lead_source_id' => $leadSource->id]);

       $this->assertInstanceOf(LeadSource::class, $lead->leadSource);
       $this->assertEquals($leadSource->name, $lead->leadSource->name);

   }

   public function testLeadHasJobType()
   {
       $lead = factory(Lead::class)->create();
       factory(JobType::class)->create(['lead_id' => $lead->id]);

       $this->assertInstanceOf(JobType::class, $lead->jobType);
   }

   public function testLeadHasAppointment()
   {
        $lead = factory(Lead::class)->create();
        factory(Appointment::class)->create(['lead_id' => $lead->id]);

        $this->assertInstanceOf(Appointment::class, $lead->appointment);
   }

   public function testLeadHasDocuments()
   {
       $lead = factory(Lead::class)->create();
       factory(Document::class, 3)->create(['lead_id' => $lead->id]);

       $this->assertContainsOnlyInstancesOf(Document::class, $lead->documents);
   }

}
