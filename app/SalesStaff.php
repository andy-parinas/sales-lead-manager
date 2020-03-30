<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesStaff extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'contact_number', 'branch_id'
    ];


    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

}
