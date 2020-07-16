<?php

namespace Tests\Unit;

use App\DesignAssessor;
use App\JobType;
use App\Lead;
use App\Product;
use App\SalesStaff;
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

    public function testJobTypeBelongsToSalesStaff()
    {
        $salesStaff = factory(SalesStaff::class)->create();
        $jobType = factory(JobType::class)->create(['sales_staff_id' => $salesStaff->id]);


        $this->assertInstanceOf(SalesStaff::class, $jobType->salesStaff);
        $this->assertEquals($salesStaff->first_name, $jobType->salesStaff->first_name);

    }

}
