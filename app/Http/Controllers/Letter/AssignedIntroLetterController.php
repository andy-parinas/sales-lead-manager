<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AssignedIntroLetterController extends Controller
{
    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
    }


    public function send(Request $request, $leadid, $salesContactId)
    {
        $salesContact = SalesContact::with('postcode')->findOrFail($salesContactId);
        $lead = Lead::findOrFail($leadid);

        $user = Auth::user();

        $to = $salesContact->email;
        $from = $user->email;

        $subject = "Spanline Home Additions Design Consultation";

        $message = "<p> Monday, November 16, 2020 </p> <br/> <br/>" .
            "<div>{$salesContact->title}. {$salesContact->frist_name} {$salesContact->last_name} </div>" .
            "<div>{$salesContact->street1}, {$salesContact->street2}</div>" .
            "<div>{$salesContact->postcode->locality}, {$salesContact->postcode->state}, {$salesContact->postcode->pcode}</div> <br/> <br/>" .
            "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>Thank you for considering Spanline Home Additions for your proposed home addition requirement<p>" .
            "<p>In this day and age, we often find people are unsure as to what to expect from a Home Addition Design Consultation.
                 As a result it is a Spanline Customer
                 Service Code Standard that I outline to you the full extent of our service
                 commitment to you.</p>".
            "<p>You will soon be contacted by one of our fully qualified Design Assessors who
                will be able to provide assistance to you.</p>" .
            "<p>You are assured that our Design Assessor will meet with you at your agreed
                appointment time and may be identified by Spanlines Photo ID card. These
                are only issued to Accredited Spanline Design Assessors under the strictest
                security. You are also assured that they will listen to all your requirements and
                needs and will specifically take into account your wishes before offering any
                advice or ideas for your consideration.</p>".
            "<p>At Spanline, we believe there should be no secrets or matters that should not
                be brought out in the open, so the Design Assessor will explain to you exactly
                what the Spanline product is, how it performs and what you can expect from a
                Spanline Home Addition.</p>" .
            "<p>Thankyou for giving us an opportunity to assess and advise you on your project.</p> <br/> <br/>".
            "<p>Regards,</p> <br/>" .
            "<div>Spanline Home Additions</div><div>Franchise Manager</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);

        $lead->update([
            'assigned_intro_sent' => date("Y-m-d")
        ]);

        $lead->refresh();

        Log::info("Unassigned Intro Letter Sent {$to}");

        return response(['data' => $lead], Response::HTTP_OK);
    }
}
