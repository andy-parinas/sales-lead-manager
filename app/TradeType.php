<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeType extends Model
{
    protected $fillable = [
        'name'
    ];


    public function tradeStaffs()
    {
        return $this->hasMany(TradeStaff::class);
    }
}
