<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Postcode extends JsonResource
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
            'postcode' => $this->pcode,
            'suburb' => $this->locality,
            'state' => $this->state,
            'title' => $this->locality . ', ' . $this->state . ', ' . $this->pcode
        ];
    }
}
