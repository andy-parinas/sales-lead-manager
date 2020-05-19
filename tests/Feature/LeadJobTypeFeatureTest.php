<?php

namespace Tests\Feature;

use App\DesignAssessor;
use App\JobType;
use App\Lead;
use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadJobTypeFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanUpdateLeadJobType()
    {
        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $jobType = factory(JobType::class)->create(['lead_id' => $lead->id]);

        $product = factory(Product::class)->create();
        $designAssessor = factory(DesignAssessor::class)->create();


        $updates = [
            'taken_by' => 'Andy Parinas',
            'date_allocated' => '2020-05-19',
            'product_id' => $product->id,
            'design_assessor_id' => $designAssessor->id
        ];

        $this->authenticateStaffUser();

        $response  = $this->patch('api/leads/' . $lead->id . '/job-types/' . $jobType->id, $updates);


        $response->assertStatus(Response::HTTP_OK);

        $jobType->refresh();

        $this->assertEquals($updates['taken_by'], $jobType->taken_by);
        $this->assertEquals($updates['date_allocated'], $jobType->date_allocated);
        $this->assertEquals($updates['product_id'], $jobType->product_id);
        $this->assertEquals($updates['design_assessor_id'], $jobType->design_assessor_id);


    }

}
