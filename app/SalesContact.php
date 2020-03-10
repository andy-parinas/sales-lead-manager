<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesContact extends Model
{
    protected $fillable = [
        'title', 'first_name', 'last_name', 'email', 'contact_number'
    ];


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTitledFullNameAttribute()
    {
        return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

}
