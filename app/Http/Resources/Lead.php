<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Lead extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
           'details' => [
               'id' => $this->id,
               'leadNumber' => $this->lead_number,
               'leadDate' => $this->lead_date,
               'postcodeStatus' => $this->postcode_status,
               'franchiseId' => $this->franchise->id,
               'franchiseNumber' => $this->franchise->franchise_number,
               'leadSourceId' =>$this->leadSource->id,
               'leadSource' => $this->leadSource->name,
               'firstName' => $this->salesContact->first_name,
               'lastName' => $this->salesContact->last_name,
               'email' => $this->salesContact->email,
               'email2' => $this->salesContact->email2,
               'contactNumber' => $this->salesContact->contact_number,
               'street1' => $this->salesContact->street1,
               'street2' => $this->salesContact->street2,
               'suburb' => $this->salesContact->suburb,
               'state' => $this->salesContact->state,
               'postcode' => $this->salesContact->postcode,
               'customerType' => $this->salesContact->customer_type,
               'created_at' => $this->created_at
           ],
            'jobType' => $this->jobType ? [
                'id' => $this->jobType->id,
                'takenBy' => $this->jobType->taken_by,
                'dateAllocated' => $this->jobType->date_allocated,
                'description' => $this->jobType->description,
                'productId' => $this->jobType->product->id,
                'product' => $this->jobType->product->name,
                'designAssessorId' => $this->jobType->designAssessor->id,
                'designAssessor' => $this->jobType->designAssessor->full_name,
                'designAssessorEmail' => $this->jobType->designAssessor->email,
                'designAssessorContactNumber' => $this->jobType->designAssessor->contact_number
            ] : null,
            'appointment' => $this->appointment ? [
                'id' => $this->appointment->id,
                'appointmentDate' => $this->appointment->getDateString(),
                'appointmentTime' => $this->appointment->getTimeString(),
                'date' => $this->appointment->appointment_date,
                'notes' => $this->appointment->appointment_notes,
                'quotedPrice' => $this->appointment->quoted_price,
                'outcome' => $this->appointment->outcome,
                'comments' => $this->appointment->comments
            ] : null
        ];
    }
}
