<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    


    public function franchises()
    {
        return $this->belongsToMany(Franchise::class);
    }

}
