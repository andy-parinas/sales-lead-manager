<?php

namespace Tests\Feature;

use App\Contract;
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

        $this->assertEquals(75,$contract->total_contract);

    }

    public function testCanNotCreateContractIfDepositIsMoreThanContract()
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

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, Contract::all());
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

    }
}
