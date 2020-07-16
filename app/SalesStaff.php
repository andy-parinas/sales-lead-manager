<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesStaff extends Model
{
    const ACTIVE = 'active';
    const BLOCKED = 'blocked';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'contact_number', 'franchise_id', 'status'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function jobTypes()
    {
        return $this->hasMany(JobType::class);
    }

}
