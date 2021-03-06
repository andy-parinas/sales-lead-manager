<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMade extends Model
{
    protected $fillable = [
        'payment_date',
        'description',
        'amount'
    ];

    public function finance()
    {
        return $this->belongsTo(Finance::class);
    }
}
