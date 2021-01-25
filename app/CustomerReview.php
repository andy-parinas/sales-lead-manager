<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    protected $fillable = [
        'date_project_completed',
        'date_warranty_received',
        'home_addition_type',
        'home_addition_description',
        'service_received_rating',
        'workmanship_rating',
        'finished_product_rating',
        'design_consultant_rating',
        'maintenance_letter_sent',
        'comments'
    ];



    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
