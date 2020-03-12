<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{


    protected $fillable = [
        'number',
        'name',
        'description'
    ];


    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
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
        return $this->hasMany(SalesStaff::class);
    }

}
