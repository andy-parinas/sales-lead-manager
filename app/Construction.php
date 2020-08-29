<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Construction extends Model
{
    protected $guarded = [];


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function postcode()
    {
        return $this->belongsTo(Postcode::class);
    }

    public function tradeStaff()
    {
        return $this->belongsTo(TradeStaff::class);
    }

    public function buildLogs()
    {
        return $this->hasMany(BuildLog::class);
    }

}
