<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoofColour extends Model
{
    protected $fillable = ['name'];


    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }


}
