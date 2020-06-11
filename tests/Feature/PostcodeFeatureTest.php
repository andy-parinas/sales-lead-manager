<?php

namespace Tests\Feature;

use App\Postcode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class PostcodeFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function testCanSearchPostcodesByAuthenticatedUsers()
    {
        $this->authenticateStaffUser();

        factory(Postcode::class)->create(['pcode' => '123456']);
        factory(Postcode::class,10)->create();

        $this->get('api/postcodes?search=' . '123456')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

    }

    public function testCanListPostcodes(){

        $this->authenticateStaffUser();

        factory(Postcode::class, 15)->create();

        $response  = $this->get('/api/postcodes?size=10');

        dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }


}
