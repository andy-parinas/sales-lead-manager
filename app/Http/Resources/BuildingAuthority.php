<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BuildingAuthority extends JsonResource
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
            'approvalRequired' => $this->approval_required,
            'buildingAuthorityName' => $this->building_authority_name,
            'datePlansSentToDraftsman' => $this->date_plans_sent_to_draftsman,
            'datePlansCompleted' => $this->date_plans_completed,
            'datePlansSentToAuthority' => $this->date_plans_sent_to_authority,
            'buildingAuthorityComments' => $this->building_authority_comments,
            'dateAnticipatedApproval' => $this->date_anticipated_approval,
            'dateReceivedFromAuthority' => $this->date_received_from_authority,
            'permitNumber' => $this->permit_number,
            'securityDepositRequired' => $this->security_deposit_required,
            'buildingInsuranceName' => $this->building_insurance_name,
            'buildingInsuranceNumber' => $this->building_insurance_number,
            'dateInsuranceRequestSent' => $this->date_insurance_request_sent,
        ];
    }
}
