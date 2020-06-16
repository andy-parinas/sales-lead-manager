<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeStaff extends Model
{
    const ACTIVE = 'active';
    const BLOCKED = 'blocked';

    protected $fillable = [
      'first_name', 'last_name', 'email', 'contact_number', 'trade_type_id', 'franchise_id',
        'company', 'abn', 'builders_license', 'status'
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
