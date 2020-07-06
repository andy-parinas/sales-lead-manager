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

class LeadFinanceFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanCreateFinanceFromContract()
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

        $finance = $lead->finance;

        self::assertInstanceOf(Finance::class, $finance);
        $this->assertEquals(100/1.1, $finance->project_price);
        $this->assertEquals(75, $finance->balance);

    }

    public function testCanUpdateFinanceOnContractUpdate()
    {
        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_date' => '06/29/2020',
            'contract_number' => 'AAA 111',
            'contract_price' => 100.00,
            'deposit_amount' => 25,
            'total_variation' => 0,
            'total_contract' => 100.00,
            'warranty_required' => 'yes',
            'date_deposit_received' => '06/29/2020',
            'date_warranty_sent' => '06/29/2020',
            'lead_id' => $lead->id
        ]);

        $project_price = $contract->contract_price / 1.1;
        $gst = $project_price * 0.10;

        $finance = factory(Finance::class)->create([
            'project_price' => $project_price,
            'gst' => $gst,
            'contract_price' => $contract->contract_price,
            'total_contract' => $contract->total_contract,
            'deposit' => $contract->deposit_amount,
            'balance' =>  $contract->total_contract - $contract->deposit_amount,
            'lead_id' => $lead->id
        ]);


        $updates = [
            'contract_price' => 200.00,
            'deposit_amount' => 75,
            'date_deposit_received' => '06/29/2020',
        ];

        $this->authenticateStaffUser();

        $response = $this->patch('api/leads/' . $lead->id . '/contracts/' . $contract->id, $updates);

        $response->assertStatus(Response::HTTP_OK);

        $finance->refresh();

        $this->assertEquals($updates['contract_price'] / 1.1, $finance->project_price);
        $this->assertEquals(($updates['contract_price'] / 1.1) * 0.1, $finance->gst);
        $this->assertEquals(200, $finance->total_contract);
        $this->assertEquals(125, $finance->balance);
    }

}
