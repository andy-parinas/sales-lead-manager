<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeStaff extends Model
{
    protected $fillable = [
      'first_name', 'last_name', 'email', 'contact_number', 'trade_type_id', 'branch_id'
    ];


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function tradeType()
    {
        return $this->belongsTo(TradeType::class);
    }

    public function franchise(){
        return $this->belongsTo(Franchise::class);
    }
}
