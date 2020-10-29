<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'design_correct',
        'date_design_check',
        'costing_correct',
        'date_costing_check',
        'estimated_build_days',
        'trades_required',
        'building_supervisor',
        'roof_sheet_id',
        'roof_colour_id',
        'lineal_metres',
        'franchise_authority',
        'authority_date',
        'date_maintenance_letter_sent'
    ];


    public function roofSheet()
    {
        return $this->belongsTo(RoofSheet::class);
    }

    public function roofColour()
    {
        return $this->belongsTo(RoofColour::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
