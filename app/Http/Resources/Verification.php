<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Verification extends JsonResource
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
            'designCorrect' => $this->design_correct,
            'dateDesignCheck' => $this->date_design_check,
            'costingCorrect' => $this->costing_correct,
            'dateCostingCheck' => $this->date_costing_check,
            'estimatedBuildDays' => $this->estimated_build_days,
            'tradesRequired' => $this->trades_required,
            'buildingSupervisor' => $this->building_supervisor,
            'linealMetres' => $this->lineal_metres,
            'franchiseAuthority' => $this->franchise_authority,
            'authorityDate' => $this->authority_date,
            'roofSheetId' => $this->roof_sheet_id,
            'roofSheet' => $this->roofSheet? $this->roofSheet->name : null,
            'roofColourId' => $this->roof_colour_id,
            'roofColour' => $this->roofColour? $this->roofColour->name : null,
        ];
    }
}
