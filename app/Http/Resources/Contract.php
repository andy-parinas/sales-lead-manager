<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Contract extends JsonResource
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
             'contractDate' => $this->contract_date,
             'contractNumber' => $this->contract_number,
             'contractPrice' => $this->contract_price,
             'depositAmount' => $this->deposit_amount,
             'dateDepositReceived' => $this->date_deposit_received,
             'totalContract' => $this->total_contract,
             'warrantyRequired' => $this->warranty_required,
             'dateWarrantySent' => $this->date_warranty_sent,
        ];
    }
}
