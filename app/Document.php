<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title', 'path', 'type', 'lead_id', 'description'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
