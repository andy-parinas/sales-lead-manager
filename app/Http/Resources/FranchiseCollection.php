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
                    'parentId' => $franchise->parent_id,
                    'type' => $franchise->isParent() ? 'Main Franchise' : 'Sub-Franchise',
                    'parent'=> $franchise->parent? $franchise->parent->franchise_number : null
                ];
            }),
            'pagination' => [
                'total' => method_exists($this, 'total') ? $this->total() : 0,
                'count' => method_exists($this, 'count') ? $this->count() : 0,
                'per_page' => method_exists($this, 'perPage') ? $this->perPage() : 0,
                'current_page' => method_exists($this, 'currentPage') ? $this->currentPage() : 0,
                'total_pages' => method_exists($this, 'lastPage') ? $this->lastPage() : 0
            ]
        ];
    }
}
