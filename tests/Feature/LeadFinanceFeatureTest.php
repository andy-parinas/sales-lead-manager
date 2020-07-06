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

}
