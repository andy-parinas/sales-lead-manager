<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    protected $fillable = [
        'number',
        'branch_id',
        'sales_contact_id',
        'lead_source_id'
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesContact()
    {
        return $this->belongsTo(SalesContact::class);
    }


    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

}
