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
            'id' => $this->id,
            'leadNumber' => $this->lead_number,
            'leadDate' => $this->lead_date,
            'postcodeStatus' => $this->postcode_status,
            'franchiseNumber' => $this->franchise->franchise_number,
            'leadSource' => $this->leadSource->name,
            'firstName' => $this->salesContact->first_name,
            'lastName' => $this->salesContact->last_name,
            'email' => $this->salesContact->email,
            'contactNumber' => $this->salesContact->contact_number,
            'postcode' => $this->salesContact->postcode,
            'jobType' => $this->jobType ? [
                'takenBy' => $this->jobType->taken_by,
                'dateAllocated' => $this->jobType->date_allocated,
                'description' => $this->jobType->description,
                'productId' => $this->jobType->product->id,
                'product' => $this->jobType->product->name,
                'designAdvisorId' => $this->jobType->designAssessor->id,
                'designAdvisor' => $this->jobType->designAssessor->full_name
            ] : null,
            'appointment' => $this->appointment ? [
                'date' => $this->appointment->appointment_date,
                'notes' => $this->appointment->appointment_notes,
                'quotedPrice' => $this->appointment->quoted_price,
                'outcome' => $this->appointment->outcome,
                'comments' => $this->appointment->comments
            ] : null
        ];
    }
}
