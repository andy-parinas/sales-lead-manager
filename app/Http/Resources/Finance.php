<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Finance extends JsonResource
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
            'projectPrice' => $this->project_price,
            'gst' => $this->gst,
            'contractPrice' => $this->contract_price,
            'totalContract' => $this->total_contract,
            'deposit' => $this->deposit,
            'totalPaymentMade' => $this->total_payment_made,
            'balance' => $this->balance,
        ];

    }
}
