<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SalesStaffCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function($staff){
                return [
                    'id' => $staff->id,
                    'key' => uniqid(),
                    'firstName' => $staff->first_name,
                    'lastName' => $staff->last_name,
                    'email' => $staff->email,
                    'contactNumber' => $staff->contact_number,
                    'status' => $staff->status,
                    'franchises' =>$staff->franchise_number
                ];
            }),
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage()
            ]
        ];
    }
}
