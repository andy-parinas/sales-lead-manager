<?php

namespace Tests\Feature;

use App\Franchise;
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

        $this->get('api/postcodes/search?search=' . '123456')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

    }

    public function testCanListPostcodes(){

        $this->authenticateStaffUser();

        factory(Postcode::class, 15)->create();

        $response  = $this->get('/api/postcodes?size=10');


        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }

    public function testCanCheckFranchisePostcode(){

        $this->authenticateHeadOfficeUser();

        $postcode = factory(Postcode::class)->create();
        $postcode2 = factory(Postcode::class)->create();

        $franchise = factory(Franchise::class)->create();

        $franchise->postcodes()->attach($postcode->id);

        $response  = $this->get('/api/franchises/' . $franchise->id . '/postcodes/' .$postcode->id . '/check');

        $result = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(true, $result);

        $response  = $this->get('/api/franchises/' . $franchise->id . '/postcodes/' .$postcode2->id . '/check');

        $result = json_decode($response->content());
        $this->assertEquals(false, $result);

    }


}
