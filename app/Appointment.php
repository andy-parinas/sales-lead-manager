<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_date', 'appointment_notes', 'quoted_price', 'outcome', 'comments', 'lead_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
