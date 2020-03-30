<?php

namespace Tests\Feature;

use App\Franchise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FranchiseFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testCanListFranchise()
    {
        factory(Franchise::class, 30)->create();

        $response = $this->get('api/franchises');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(30);

    }

}
