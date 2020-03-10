<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{


    protected $fillable = [
        'number',
        'name',
        'description'
    ];


    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

}
