<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesContact extends Model
{

    const RESIDENTIAL = 'residential';
    const COMMERCIAL = 'commercial';

    const ACTIVE = 'active';
    const ARCHIVED = 'archived';

    protected $fillable = [
        'title', 'first_name', 'last_name', 'email', 
        'contact_number', 'street1', 'street2', 'suburb', 'state', 'postcode', 'customer_type'
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
