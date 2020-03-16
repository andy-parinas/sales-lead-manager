<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title', 'path', 'type', 'lead_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
