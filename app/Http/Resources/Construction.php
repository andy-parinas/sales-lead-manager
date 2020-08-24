<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Construction extends JsonResource
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
            'siteAddress' => $this->site_address,
            'postcode' => [
                'id' => $this->postcode->id,
                'suburb' => $this->postcode->locality,
                'state' => $this->postcode->state,
                'postcode' => $this->postcode->pcode
            ],
            'materialList' => $this->material_list,
            'dateMaterialsReceived' => $this->date_materials_received,
            'dateAssemblyCompleted' => $this->date_assembly_completed,
            'dateAnticipatedDelivery' => $this->date_anticipated_delivery,
            'dateFinishedProductDelivery' => $this->date_finished_product_delivery,
            'coilNumber' => $this->coil_number,
            'tradeStaff' => [
                'id' => $this->tradeStaff->id,
                'name' => $this->tradeStaff->fullName,
                'email' => $this->tradeStaff->email
            ],
            'anticipatedConstructionStart' => $this->anticipated_construction_start,
            'anticipatedConstructionComplete' => $this->anticipated_construction_complete,
            'actualConstructionStart' => $this->actual_construction_start,
            'actualConstructionComplete' => $this->actual_construction_complete,
            'comments' => $this->comments,
            'finalInspectionDate' => $this->final_inspection_date
        ];
    }
}
