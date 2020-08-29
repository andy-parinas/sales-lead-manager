<?php

namespace Tests\Feature;

use App\Construction;
use App\Lead;
use App\Postcode;
use App\TradeStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class ConstructionFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanShowLeadConstruction()
    {


        $lead = factory(Lead::class)->create();

        $construction = factory(Construction::class)->create([
            'lead_id' => $lead->id
        ]);

        $this->authenticateHeadOfficeUser();

        $response = $this->get('/api/leads/' . $lead->id . '/constructions');


        $response->assertStatus(Response::HTTP_OK);

    }

    public function testCanShowNoContentLeadWithoutConstruction()
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $this->authenticateHeadOfficeUser();

        $response = $this->get('/api/leads/' . $lead->id . '/constructions');


        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testCanCreateConstruction()
    {

        $tradeStaff = factory(TradeStaff::class)->create();
        $lead = factory(Lead::class)->create();
        $postcode = factory(Postcode::class)->create();

        $data = [
            'site_address' => '123 Sesame Street',
            'postcode_id' => $postcode->id,
            'trade_staff_id' => $tradeStaff->id,
        ];

        $this->authenticateHeadOfficeUser();

        $this->post('api/leads/' . $lead->id . '/constructions', $data)
            ->assertStatus(Response::HTTP_CREATED);


        $this->assertCount(1, Construction::all());


    }

    public function testTradeStaffScheduleIsCreatedWhenConstructionIsCreated()
    {

        $tradeStaff = factory(TradeStaff::class)->create();
        $lead = factory(Lead::class)->create();
        $postcode = factory(Postcode::class)->create();

        $data = [
            'site_address' => '123 Sesame Street',
            'postcode_id' => $postcode->id,
            'trade_staff_id' => $tradeStaff->id,
        ];

        $this->authenticateHeadOfficeUser();

        $this->post('api/leads/' . $lead->id . '/constructions', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $tradeStaff->refresh();

        $this->assertCount(1, $tradeStaff->tradeStaffSchedules);



    }

}
