<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TradeStaffCollection extends ResourceCollection
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
                    'contactNumber' => $staff->contact_number,
                    'franchiseId' => $staff->franchise_id,
                    'franchise' => $staff->franchise_number,
                    'company' => $staff->company,
                    'abn' => $staff->abn,
                    'buildersLicense' => $staff->builders_license,
                    'status' => $staff->status,
                    'tradeType' => $staff->trade_type,
                    'tradeTypeId' => $staff->trade_type_id,
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
