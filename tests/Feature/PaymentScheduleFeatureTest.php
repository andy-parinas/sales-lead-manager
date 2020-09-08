<?php

namespace Tests\Feature;

use App\Finance;
use App\PaymentMade;
use App\PaymentSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class PaymentScheduleFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanCreatePaymentSchedule()
    {

        $this->withoutExceptionHandling();

        $finance = factory(Finance::class)->create();

        $data = factory(PaymentSchedule::class)->raw();

        $this->authenticateHeadOfficeUser();

        $this->post('api/finances/' . $finance->id . '/payment-schedules', $data)
            ->assertStatus(Response::HTTP_CREATED);


        $this->assertCount(1, $finance->paymentsSchedule);


    }


    public function testCanListPaymentSchedules()
    {

        $this->withoutExceptionHandling();

        $finance = factory(Finance::class)->create();

        factory(PaymentSchedule::class, 5)->create([
            'finance_id' => $finance->id
        ]);

        $response = $this->get('api/finances/' . $finance ->id . '/payment-schedules');


        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');



    }

    public function testCanPayPaymentSchedule()
    {
        $contract_price = 10000;
        $project_price = $contract_price / 1.1;
        $gst = $project_price * 0.10;
        $deposit = $deposit = 0;
        $balance = $contract_price - $deposit;

        $finance = factory(Finance::class)->create([
            'project_price' => $project_price,
            'gst' => $gst,
            'contract_price' => $contract_price,
            'total_contract' => $contract_price,
            'deposit' => $deposit,
            'balance' => $balance,
            'total_payment_made' => 0,
        ]);

        $paymentSchedule = factory(PaymentSchedule::class)->create([
            'finance_id' => $finance->id,
            'amount' => 1000
        ]);


        $paymentData = [
            'payment' => 100
        ];

        $this->post("api/finances/{$finance->id}/payment-schedules/{$paymentSchedule->id}/pay", $paymentData)
            ->assertStatus(Response::HTTP_CREATED);

        $finance->refresh();
        $paymentSchedule->refresh();

        $this->assertCount(1, $finance->paymentsMade);
        $this->assertEquals(900, $paymentSchedule->balance);
        $this->assertEquals(100, $paymentSchedule->payment);


        $paymentMade = PaymentMade::first();

        $this->assertEquals(100, $paymentMade->amount);


    }

}
