<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    function testCanCreateProductByHeadOffice()
    {
        $data = factory(Product::class)->raw(['name' => 'product1']);

        $this->authenticateHeadOfficeUser();


        $this->post('api/products', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, Product::all());

    }

    public function testCanNotCreateProductByNonHeadOffice()
    {
        $data = factory(Product::class)->raw(['name' => 'product1']);


        $this->authenticateFranchiseAdmin();
        $this->post('api/products', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Product::all());

        $this->authenticateStaffUser();
        $this->post('api/products', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, Product::all());

    }


}
