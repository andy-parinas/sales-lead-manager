<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'project_price',
        'gst',
        'contract_price',
        'total_contract',
        'deposit',
        'balance',
    ];


    public function lead(){
        return $this->belongsTo(Lead::class);
    }

}
