<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DesignAssessor extends Model
{
    protected $fillable = [
        'first_name','last_name','email','contact_number'
    ];


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }



}
