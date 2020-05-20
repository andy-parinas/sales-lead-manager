<?php

namespace Tests\Feature;

use App\Appointment;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadAppointmentFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanUpdateLeadAppointment()
    {

        $lead = factory(Lead::class)->create();
        $appointment = factory(Appointment::class)->create(['lead_id' => $lead->id, 'outcome' => 'pending']);

        $updates =  [
            'appointment_date' => '2020-05-20 12:00',
            'appointment_notes' => 'updated notes',
            'quoted_price' => 123,
            'outcome' => 'success',
            'comments' => 'Updated Comments'
        ];

        $this->authenticateStaffUser();


        $response = $this->put('api/leads/' . $lead->id . '/appointments/' . $appointment->id, $updates);

        //dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK);

        $appointment->refresh();

        $this->assertEquals($updates['appointment_date'], $appointment->appointment_date);
        $this->assertEquals($updates['appointment_notes'], $appointment->appointment_notes);
        $this->assertEquals($updates['quoted_price'], $appointment->quoted_price);
        $this->assertEquals($updates['outcome'], $appointment->outcome);
        $this->assertEquals($updates['comments'], $appointment->comments);
    }
}
