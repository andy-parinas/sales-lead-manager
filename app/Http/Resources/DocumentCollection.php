<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function($document){
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'path' => $document->path,
                    'type' => $document->type,
                    'description' => $document->description
                ];
            }),
        ];
    }
}
