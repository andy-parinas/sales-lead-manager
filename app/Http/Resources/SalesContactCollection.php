<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SalesContactCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function($contact){
                return [
                    'id' => $contact->id,
                    'firstName' => $contact->first_name,
                    'lastName' => $contact->last_name,
                    'email' => $contact->email,
                    'email2' => $contact->email2,
                    'contactNumber' => $contact->contact_number,
                    'street1' => $contact->street1,
                    'street2' => $contact->street2,
                    'suburb' => $contact->suburb,
                    'state' => $contact->state,
                    'postcode' => $contact->postcode,
                    'customerType' => $contact->customer_type,
                    'status' => $contact->status
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
