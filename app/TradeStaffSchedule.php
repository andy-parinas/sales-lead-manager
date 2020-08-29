<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeStaffSchedule extends Model
{
    protected $fillable = [
        'job_number',
        'anticipated_start',
        'actual_start',
        'anticipated_end',
        'actual_end',
        'trade_staff_id',
    ];

    public function tradeStaff()
    {
        return $this->belongsTo(TradeStaff::class);
    }


}
