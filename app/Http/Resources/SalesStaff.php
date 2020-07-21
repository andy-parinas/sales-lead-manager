<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesStaff extends JsonResource
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
            'contactNumber' => $this->contact_number,
            'status' => $this->status,
            'franchiseId' => $this->franchise_id,
            'franchise' => $this->franchise->franchise_number,
            'title' => $this->first_name . " " . $this->last_name
        ];
    }
}
