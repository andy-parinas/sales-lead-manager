<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    protected $fillable = [
        'taken_by',
        'date_allocated',
        'lead_id',
        'product_id',
        'sales_staff_id',
        'description',
        'email_sent_to_design_advisor',
        'sms_sent_to_design_advisor'
    ];


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

//    public function designAssessor()
//    {
//        return $this->belongsTo(DesignAssessor::class);
//    }

    public function salesStaff()
    {
        return $this->belongsTo(SalesStaff::class);
    }

}
