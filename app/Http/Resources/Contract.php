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
             'contractDate' => 'contract_date',
             'contractNumber' => 'contract_number',
             'contractPrice' => 'contract_price',
             'depositAmount' => 'deposit_amount',
             'dateDepositReceived' => 'date_deposit_received',
             'totalContract' => 'total_contract',
             'warrantyReceived' => 'warranty_required',
             'dateWarrantySent' => 'date_warranty_sent',
        ];
    }
}
