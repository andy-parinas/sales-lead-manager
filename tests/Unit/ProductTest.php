<?php

namespace Tests\Unit;

use App\JobType;
use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function testProductHasJobType()
    {

        $product = factory(Product::class)->create();
        factory(JobType::class, 3)->create(['product_id' => $product->id]);

        $this->assertContainsOnlyInstancesOf(JobType::class, $product->jobTypes);
        $this->assertCount(3, $product->jobTypes);

    }
}
