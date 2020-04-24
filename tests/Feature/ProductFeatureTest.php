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

    public function testCanListProductByAuthenticatedUsers()
    {
        factory(Product::class,5)->create();

        $this->authenticateStaffUser();
        $this->get('api/products')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');
    }

    public function testCanNotListProductByNonAuthenticatedUsers()
    {

        factory(Product::class,5)->create();

        $this->get('api/products')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCanUpdateProductByHeadOfficeUser()
    {
        $product = factory(Product::class)->create();

        $this->authenticateHeadOfficeUser();

        $this->put('api/products/' . $product->id, ['name' => 'update', 'description' => 'update'])
            ->assertStatus(Response::HTTP_OK);

        $product->refresh();

        $this->assertEquals('update', $product->name);
        $this->assertEquals('update', $product->description);

    }

    public function testCanNotUpdateProductByNonHeadOffice()
    {
        $product = factory(Product::class)->create();

        $this->authenticateStaffUser();
        $this->put('api/products/' . $product->id, ['name' => 'update', 'description' => 'update'])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->authenticateFranchiseAdmin();
        $this->put('api/products/' . $product->id, ['name' => 'update', 'description' => 'update'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanDeleteProductByHeadOffice()
    {
        $product = factory(Product::class)->create();

        $this->authenticateHeadOfficeUser();

        $this->delete('api/products/' . $product->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(0, Product::all());

    }

    public function testCanNotDeleteProductByNonHeadOffice()
    {
        $product = factory(Product::class)->create();

        $this->authenticateFranchiseAdmin();
        $this->delete('api/products/' . $product->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Product::all());

        $this->authenticateStaffUser();
        $this->delete('api/products/' . $product->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, Product::all());
    }

}
