<?php

namespace App\Http\Controllers\Lead;

use App\Appointment;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Http\Resources\Lead as LeadResource;

class LeadAppointmentController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function update(Request $request, $leadId, $appointmentId)
    {
        $lead = Lead::with(['jobType', 'appointment', 'documents'])->findOrFail($leadId);
        $appointment = Appointment::findOrFail($appointmentId);

        if($lead->appointment->id != $appointment->id){
            throw new BadRequestHttpException("The Appointment is not associated with the lead");
        }

        $data = $this->validate($request, [
            'appointment_date' => 'date',
            'appointment_notes' => '',
            'quoted_price' => '',
            'outcome' => ''
        ]);

        $appointment->update($data);

        $lead->refresh();

        return $this->showOne(new LeadResource($lead));

    }


}
