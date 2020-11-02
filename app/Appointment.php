<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_date', 'appointment_notes', 'quoted_price', 'outcome', 'comments', 'lead_id', 'followup_date'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }


    public function getDateAttribute()
    {
        $dateArray = explode(" ", $this->appointment_date);

        if(count($dateArray) >= 2){
            return $dateArray[0];
        }

        return '';
    }

    public function getTimeAttribute()
    {
        $dateArray = explode(" ", $this->appointment_date);
        if(count($dateArray) >= 2){
            return $dateArray[1];
        }

        return '';
    }

    public function getFollowUpDateStringAttribute()
    {
        $dateArray = explode(" ", $this->followup_date);

        if(count($dateArray) >= 2){
            return $dateArray[0];
        }

        return '';
    }

    public function getFollowUpTimeAttribute()
    {
        $dateArray = explode(" ", $this->followup_date);
        if(count($dateArray) >= 2){
            return $dateArray[1];
        }

        return '';
    }

    public function getDateString()
    {
        $dateArray = explode(" ", $this->appointment_date);

        if(count($dateArray) >= 2){
            return $dateArray[0];
        }

        return '';
    }

    public function getTimeString()
    {
        $dateArray = explode(" ", $this->appointment_date);
        if(count($dateArray) >= 2){
            return $dateArray[1];
        }

        return '';
    }
}
