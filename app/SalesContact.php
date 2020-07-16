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
        'title', 'first_name', 'last_name', 'email', 'email2',
        'contact_number', 'street1', 'street2', 'postcode_id', 'customer_type', 'status'
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

    public function postcode()
    {
        return $this->belongsTo(Postcode::class);
    }

}
