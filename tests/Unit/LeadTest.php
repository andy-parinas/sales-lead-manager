<?php

namespace Tests\Unit;

use App\Branch;
use App\Lead;
use App\LeadSource;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LeadTest extends TestCase
{
   use RefreshDatabase;

   public function testLeadBelongsToABranch()
   {
       $branch = factory(Branch::class)->create();
       $lead = factory(Lead::class)->create(['branch_id' => $branch->id]);

       $this->assertInstanceOf(Branch::class, $lead->branch);
       $this->assertEquals($branch->number, $lead->branch->number);
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

}
