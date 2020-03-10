<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    protected $fillable = [
        'number',
        'branch_id',
        'sales_contact_id'
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesContact()
    {
        return $this->belongsTo(SalesContact::class);
    }

}
