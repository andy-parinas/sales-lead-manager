<?php

namespace Tests\Unit;

use App\Lead;
use App\LeadSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LeadSourceTest extends TestCase
{
    use RefreshDatabase;

    public function testLeadSourceHasLeads()
    {
        $leadSource = factory(LeadSource::class)->create();
        factory(Lead::class, 3)->create(['lead_source_id' => $leadSource->id]);


        $this->assertContainsOnlyInstancesOf(Lead::class, $leadSource->leads);

    }
}
