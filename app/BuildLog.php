<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuildLog extends Model
{
    protected $fillable = [
        'work_date',
        'time_spent',
        'hourly_rate',
        'total_cost',
        'construction_id',
        'trade_staff_id',
    ];

    public function construction()
    {
        return $this->belongsTo(Construction::class);
    }


    public function tradeStaff()
    {
        return $this->belongsTo(TradeStaff::class);
    }


}
