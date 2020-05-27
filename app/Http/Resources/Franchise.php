<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Franchise extends JsonResource
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
            'franchiseNumber' => $this->franchise_number,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->isParent() ? 'Main Franchise' : 'Sub-Franchise',
            'parent'=> $this->parent ? $this->parent->franchise_number : null
        ];
    }
}
