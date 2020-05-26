<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $type = explode("_", $this->user_type);
        // dd($type);
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'userType' => $this->user_type,
            'email' => $this->email
        ];
    }
}
