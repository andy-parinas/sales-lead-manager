<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesContact extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'email2' => $this->email2,
            'contactNumber' => $this->contact_number,
            'street1' => $this->street1,
            'street2' => $this->street2,
            'suburb' => $this->postcode->locality,
            'state' => $this->postcode->state,
            'postcode' => $this->postcode->pcode,
            'postcodeId' => $this->postcode->id,
            'customerType' => $this->customer_type,
            'status' => $this->status,
            'leads' => $this->whenLoaded('leads')
        ];
    }
}
