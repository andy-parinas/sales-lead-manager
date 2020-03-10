<?php

namespace Tests\Unit;

use App\Branch;
use App\Lead;
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

}
