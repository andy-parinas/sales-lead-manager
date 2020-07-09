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
}
