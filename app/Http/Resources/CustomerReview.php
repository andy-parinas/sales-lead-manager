<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerReview extends JsonResource
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
            'dateProjectCompleted' => $this->date_project_completed,
            'dateWarrantyReceived' => $this->date_warranty_received,
            'homeAdditionType' => $this->home_addition_type,
            'homeAdditionDescription' => $this->home_addition_description,
            'serviceReceivedRating' => $this->service_received_rating,
            'workmanshipRating' => $this->workmanship_rating,
            'finishedProductRating' => $this->finished_product_rating,
            'designConsultantRating' => $this->design_consultant_rating,
            'dateMaintenanceLetterSent' => $this->maintenance_letter_sent,
            'comments' => $this->comments,

        ];
    }
}
