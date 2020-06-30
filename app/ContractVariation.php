<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractVariation extends Model
{

    protected $fillable = [
        'variation_date',
        'description',
        'amount',
        'contract_id'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
