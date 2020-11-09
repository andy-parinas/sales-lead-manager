<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{


    protected $fillable = [
        'franchise_number',
        'name',
        'description',
        'parent_id'
    ];


    public function parent()
    {
        return $this->belongsTo(Franchise::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Franchise::class, 'parent_id');
    }


    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function tradeStaffs()
    {
        return $this->hasMany(TradeStaff::class);
    }

    public function salesStaffs()
    {
        return $this->belongsToMany(SalesStaff::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function postcodes()
    {
        return $this->belongsToMany(Postcode::class);
    }

    public function isParent()
    {
        return $this->parent_id == null;
    }

}
