<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuildingAuthority extends Model
{
    protected $fillable = [
        'approval_required',
        'building_authority_name',
        'date_plans_sent_to_draftsman',
        'date_plans_completed',
        'date_plans_sent_to_authority',
        'building_authority_comments',
        'date_anticipated_approval',
        'date_received_from_authority',
        'permit_number',
        'security_deposit_required',
        'building_insurance_name',
        'building_insurance_number',
        'date_insurance_request_sent',
        'intro_council_letter_sent',
        'out_of_council_letter_sent',
        'no_council_letter_sent'
    ];

    public function lead(){
        return $this->belongsTo(Lead::class);
    }
}
