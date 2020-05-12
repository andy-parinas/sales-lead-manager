<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DesignAssessorCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($assessor){
                return [
                    'id' => $assessor->id,
                    'firstName' => $assessor->first_name,
                    'lastName' => $assessor->last_name,
                    'email' => $assessor->email,
                    'contactNumber' => $assessor->contact_number
                ];
            }),
        ];
    }
}
