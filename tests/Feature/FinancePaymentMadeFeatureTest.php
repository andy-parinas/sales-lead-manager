<?php

namespace Tests\Feature;

use App\Finance;
use App\PaymentMade;
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


    public function testCanUpdatePaymentsMade()
    {

        $this->withoutExceptionHandling();

        $finance = factory(Finance::class)->create([
            'project_price' => 1000,
            'gst' => 100,
            'contract_price' => 1100,
            'total_contract' => 1100,
            'total_payment_made' => 100,
            'deposit' => 0,
            'balance' => 1000,
        ]);

        $payment = factory(PaymentMade::class)->create([
            'payment_date' => '07/09/2020',
            'description' => 'Payment 1',
            'amount' => 100,
            'finance_id' => 1
        ]);

        $update = [
            'payment_date' => '09/09/2020',
            'description' => 'Payment A',
            'amount' => 1000,
        ];

        $response = $this->patch('/api/finances/' . $finance->id . '/payments-made/' . $payment->id, $update );

        $response->assertStatus(Response::HTTP_OK);

        $finance->refresh();
        $payment->refresh();


        $this->assertEquals(1000, $finance->total_payment_made);
        $this->assertEquals(100, $finance->balance);
        $this->assertEquals(1000, $payment->amount);

    }

    public function testCanNotUpdatePaymentNotAssociatedToFinance()
    {

        $finance = factory(Finance::class)->create([
        'project_price' => 1000,
        'gst' => 100,
        'contract_price' => 1100,
        'total_contract' => 1100,
        'total_payment_made' => 100,
        'deposit' => 0,
        'balance' => 1000,
    ]);

        $payment = factory(PaymentMade::class)->create([
            'payment_date' => '07/09/2020',
            'description' => 'Payment 1',
            'amount' => 100,
        ]);

        $update = [
            'payment_date' => '09/09/2020',
            'description' => 'Payment A',
            'amount' => 1000,
        ];

        $response = $this->patch('/api/finances/' . $finance->id . '/payments-made/' . $payment->id, $update );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $finance->refresh();
        $payment->refresh();


        $this->assertEquals(100, $finance->total_payment_made);
        $this->assertEquals(1000, $finance->balance);
        $this->assertEquals(100, $payment->amount);


    }
}
