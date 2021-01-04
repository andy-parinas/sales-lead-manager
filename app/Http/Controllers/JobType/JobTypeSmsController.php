<?php

namespace App\Http\Controllers\JobType;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\SalesStaff;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JobTypeSmsController extends Controller
{

    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->middleware('auth:sanctum');
        $this->smsService = $smsService;

    }


    public function send(Request $request, $leadId, $salesStaffId)
    {

        try {

            $lead = Lead::findOrFail($leadId);
            $salesContact = $lead->salesContact;
            $salesStaff = SalesStaff::findOrFail($salesStaffId);

            $message = "LN:{$lead->lead_number} {$salesContact->first_name} {$salesContact->last_name} ";
            $message = $message . "{$salesContact->street1} {$salesContact->postcode->locality}, {$salesContact->postcode->state} ";
            $message = $message . "PH-{$salesContact->contact_number} {$salesContact->email} REQ-{$lead->jobType->product->name}";
            $message = $message . "SRC-{$lead->leadSource->name}";

            $jobType = $lead->jobType;

            $jobType->update([
                'sms_sent_to_design_advisor' => date("Y-m-d")
            ]);

           $this->smsService->sendSms($salesStaff->contact_number, $message);

            return response(['data' => $jobType->sms_sent_to_design_advisor], Response::HTTP_OK);

        }catch (\Exception $exception)
        {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Error Sending Message");
        }



    }
}
