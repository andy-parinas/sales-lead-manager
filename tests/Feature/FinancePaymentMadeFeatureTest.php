<?php

namespace Tests\Feature;

use App\Finance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class FinancePaymentMadeFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanAddPaymentMade()
    {
        $finance = factory(Finance::class)->create([
            'project_price' => 1000,
            'gst' => 100,
            'contract_price' => 1100,
            'total_contract' => 1100,
            'deposit' => 0,
            'balance' => 1100,
        ]);

        $payments = [
            'payment_date' => '07/09/2020',
            'description' => 'Payment 1',
            'amount' => 100
        ];


        $response = $this->post('/api/finances/' . $finance->id . '/payments-made', $payments);

        $response->assertStatus(Response::HTTP_CREATED);

        $finance->refresh();

        $this->assertEquals(1000, $finance->balance);
    }
}
