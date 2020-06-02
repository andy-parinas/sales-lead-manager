<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ParentFranchiseCollection extends ResourceCollection
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
                ];
            })
        ];
    }
}
