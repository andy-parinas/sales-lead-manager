<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    protected $fillable = [
      'name'
    ];


    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

}
