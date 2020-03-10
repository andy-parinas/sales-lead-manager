<?php

namespace Tests\Unit;

use App\DesignAssessor;
use App\JobType;
use App\Lead;
use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class JobTypeTest extends TestCase
{
    use RefreshDatabase;

    public function testJobTypeBelongsToLead()
    {
        $lead = factory(Lead::class)->create();
        $jobType = factory(JobType::class)->create(['lead_id' => $lead->id]);


        $this->assertInstanceOf(Lead::class, $jobType->lead);
        $this->assertEquals($lead->number, $jobType->lead->number);

    }

    public function testJobTypeBelongsToProduct()
    {
        $product = factory(Product::class)->create();
        $jobType = factory(JobType::class)->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $jobType->product);
        $this->assertEquals($product->name, $jobType->product->name);

    }

    public function testJobTypeBelongsToDesignAssessor()
    {
        $assessor = factory(DesignAssessor::class)->create();
        $jobType = factory(JobType::class)->create(['design_assessor_id' => $assessor->id]);


        $this->assertInstanceOf(DesignAssessor::class, $jobType->designAssessor);
        $this->assertEquals($assessor->first_name, $jobType->designAssessor->first_name);

    }

}
