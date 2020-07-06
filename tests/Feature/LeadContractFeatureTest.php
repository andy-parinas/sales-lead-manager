<?php

namespace Tests\Feature;

use App\Contract;
use App\Finance;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadContractFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanCreateLeadContract()
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $this->authenticateStaffUser();

        $data = [
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25.00,
            'date_deposit_received' => '06/29/2020',
            'warranty_required' => 'yes',
            'date_warranty_sent' => '06/29/2020',
        ];

        $response = $this->post('api/leads/' . $lead->id . '/contracts', $data);


        $response->assertStatus(Response::HTTP_CREATED);

        $lead->refresh();
        $contract = $lead->contract;

        $this->assertEquals(100,$contract->total_contract);

    }

    public function testCanCreateContractIfDepositIsMoreThanContract()
    {

        $lead = factory(Lead::class)->create();

        $this->authenticateStaffUser();

        $data = [
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 125.00,
            'date_deposit_received' => '06/29/2020',
            'warranty_required' => 'yes',
            'date_warranty_sent' => '06/29/2020',
        ];

        $response = $this->post('api/leads/' . $lead->id . '/contracts', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, Contract::all());
    }

    public function testCanNotCreateContractWithDepositButNoDepositDate()
    {

        $lead = factory(Lead::class)->create();

        $this->authenticateStaffUser();

        $data = [
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25.00,
            'warranty_required' => 'yes',
            'date_warranty_sent' => '06/29/2020',
        ];

        $response = $this->post('api/leads/' . $lead->id . '/contracts', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertCount(0, Contract::all());

    }

    public function testCanShowContractOfLead()
    {
        $lead = factory(Lead::class)->create();
        factory(Contract::class)->create([
            'lead_id' => $lead->id
        ]);

        $this->authenticateStaffUser();

        $response = $this->get('api/leads/' . $lead->id . '/contracts');

        $response->assertStatus(Response::HTTP_OK);

        //dd(json_decode($response->content()));
    }


    public function testCanUpdateContractChangePrice()
    {
        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25,
            'total_variation' => 0,
            'total_contract' => 75.00,
            'warranty_required' => 'yes',
            'date_deposit_received' => '06/29/2020',
            'date_warranty_sent' => '06/29/2020',
            'lead_id' => $lead->id
        ]);

        factory(Finance::class)->create(['lead_id' => $lead->id]);

        $updates = [
            'contract_price' => 150.00,
            'deposit_amount' => 25,
            'date_deposit_received' => '06/29/2020',
        ];

        $this->authenticateStaffUser();

        $response = $this->patch('api/leads/' . $lead->id . '/contracts/' . $contract->id, $updates);

        $response->assertStatus(Response::HTTP_OK);

        $contract->refresh();

        $this->assertEquals(150, $contract->total_contract);



    }


    public function testCanUpdateContractChangeDeposit()
    {

        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25,
            'total_variation' => 0,
            'total_contract' => 75.00,
            'warranty_required' => 'yes',
            'date_deposit_received' => '06/29/2020',
            'date_warranty_sent' => '06/29/2020',
            'lead_id' => $lead->id
        ]);

        factory(Finance::class)->create(['lead_id' => $lead->id]);

        $updates = [
            'contract_price' => 100.00,
            'deposit_amount' => 70,
            'date_deposit_received' => '06/29/2020',
        ];

        $this->authenticateStaffUser();

        $response = $this->patch('api/leads/' . $lead->id . '/contracts/' . $contract->id, $updates);

        $response->assertStatus(Response::HTTP_OK);

        $contract->refresh();

        $this->assertEquals(30, $contract->total_contract);

    }


    public function testCanUpdateContractChangeDepositAndPrice()
    {

        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25,
            'total_variation' => 0,
            'total_contract' => 75.00,
            'warranty_required' => 'yes',
            'date_deposit_received' => '06/29/2020',
            'date_warranty_sent' => '06/29/2020',
            'lead_id' => $lead->id
        ]);

        factory(Finance::class)->create(['lead_id' => $lead->id]);

        $updates = [
            'contract_price' => 200.00,
            'deposit_amount' => 75,
            'date_deposit_received' => '06/29/2020',
        ];

        $this->authenticateStaffUser();

        $response = $this->patch('api/leads/' . $lead->id . '/contracts/' . $contract->id, $updates);

        $response->assertStatus(Response::HTTP_OK);

        $contract->refresh();

        $this->assertEquals(200, $contract->total_contract);

    }


    public function testCanNotUpdateContractNegativeTotal()
    {

        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25,
            'total_variation' => 0,
            'total_contract' => 75.00,
            'warranty_required' => 'yes',
            'date_deposit_received' => '06/29/2020',
            'date_warranty_sent' => '06/29/2020',
            'lead_id' => $lead->id
        ]);

        $updates = [
            'contract_price' => 100.00,
            'deposit_amount' => 175,
            'date_deposit_received' => '06/29/2020',
        ];

        $this->authenticateStaffUser();

        $response = $this->patch('api/leads/' . $lead->id . '/contracts/' . $contract->id, $updates);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $contract->refresh();

        $this->assertEquals(75, $contract->total_contract);
        $this->assertEquals(25, $contract->deposit_amount);
        $this->assertEquals(100, $contract->contract_price);

    }
}
