<?php

namespace App\Http\Controllers\JobType;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesStaff;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JobTypeEmailController extends Controller
{

    protected $emailService;

    public function __construct(EmailServiceInterface $emailService)
    {
        $this->middleware('auth:sanctum');
        $this->emailService = $emailService;
    }


    public function send(Request $request, $leadId, $salesStaffId)
    {

        try {


            $lead = Lead::findOrFail($leadId);
            $salesContact = $lead->salesContact;
            $salesStaff = SalesStaff::findOrFail($salesStaffId);
            $user = Auth::user();

            $message = "<h1>A new Sales Lead has been assigned to you</h1>" .
                "<p>Lead Number: <strong>{$lead->lead_number}</strong> </p>" .
                "<p>Name: <strong>{$salesContact->full_name}</strong></p>" .
                "<p>Contact Number: <strong>{$salesContact->contact_number}</strong></p>" .
                "<p>Email: <strong>{$salesContact->email}</strong></p>" ;


            $to = $salesStaff->email;
            $from = $user->email;
            $subject = "New Sales Lead Assigned: {$lead->lead_number}";

            $this->emailService->sendEmail($to, $from, $subject, $message);

            $jobType = $lead->jobType;

            $jobType->update([
               'email_sent_to_design_advisor' => date("Y-m-d")
            ]);

            return response(['data' => $jobType->email_sent_to_design_advisor], Response::HTTP_OK);

        }catch (\Exception $exception)
        {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Error Sending Message");
        }


    }
}
