<?php

namespace Tests\Unit;

use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    
    use RefreshDatabase;
    
    public function testAppointmentBelongsToLead() {
        
        $lead = factory(\App\Lead::class)->create();
        $appointment = factory(\App\Appointment::class)->create(['lead_id' => $lead->id]);
                
        $this->assertInstanceOf(Lead::class, $appointment->lead);
        
    }
}
