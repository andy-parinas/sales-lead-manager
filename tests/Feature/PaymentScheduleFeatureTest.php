<?php

namespace Tests\Feature;

use App\Finance;
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

}
