<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TradeStaff extends JsonResource
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
            'franchiseId' => $this->franchise_id,
            'franchise' => $this->franchise->franchise_number,
            'company' => $this->company,
            'abn' => $this->abn,
            'buildersLicense' => $this->builders_license,
            'status' => $this->status,
            'tradeType' => $this->tradeType->name,
            'tradeTypeId' => $this->trade_type_id,
        ];
    }
}
