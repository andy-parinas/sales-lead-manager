<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostcodeAllCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function($postcode){
                return [
                    'id' => $postcode->id,
                    'postcode' => $postcode->postcode,
                    'suburb' => $postcode->suburb,
                    'state' => $postcode->state,
                    'title' => $postcode->suburb . ', ' . $postcode->state . ', ' . $postcode->postcode
                ];
            }),
        ];
    }
}
