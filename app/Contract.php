<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'contract_date',
        'contract_number',
        'contract_price',
        'deposit_amount',
        'date_deposit_received',
        'total_contract',
        'warranty_required',
        'date_warranty_sent',
        'lead_id',
    ];


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function contractVariations()
    {
        return $this->hasMany(ContractVariation::class);
    }
}
