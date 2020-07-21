<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SalesStaffSearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
            return $this->collection->transform(function($staff){
                return [
                    'id' => $staff->id,
                    'firstName' => $staff->first_name,
                    'lastName' => $staff->last_name,
                    'email' => $staff->email,
                    'contactNumber' => $staff->contact_number,
                    'status' => $staff->status,
                    'franchiseId' => $staff->franchise_id,
                    'title' => $staff->first_name . " " . $staff->last_name
                ];
            });
    }
}
