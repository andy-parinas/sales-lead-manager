<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TradeStaffSearchCollection extends ResourceCollection
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
                    'firstName' => $staff->first_name,
                    'lastName' => $staff->last_name,
                    'email' => $staff->email,
                    'status' => $staff->status,
                    'title' => $staff->first_name . ' ' . $staff->last_name
                ];
            }),
        ];
    }
}
