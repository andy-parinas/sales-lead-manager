<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FranchiseCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function($franchise){
                return [
                    'id' => $franchise->id,
                    'franchiseNumber' => $franchise->franchise_number,
                    'name' => $franchise->name,
                    'description' => $franchise->description,
                    'type' => $franchise->isParent() ? 'Main Franchise' : 'Sub-Franchise',
                    'parent'=> $franchise->parent? $franchise->parent->franchise_number : null
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
