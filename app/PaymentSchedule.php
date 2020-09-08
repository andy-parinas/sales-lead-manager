<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{

    const PAID = 'paid';
    const NOT_PAID = 'not_paid';

    protected $fillable = [
        'due_date',
        'payment_date',
        'description',
        'amount',
        'status',
        'payment',
        'balance'
    ];

    public function finance()
    {
        return $this->belongsTo(Finance::class);
    }
}
