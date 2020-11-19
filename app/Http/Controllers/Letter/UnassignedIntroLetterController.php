<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UnassignedIntroLetterController extends Controller
{

    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
    }

    public function send(Request $request, $salesContactId)
    {

        $salesContact = SalesContact::with('postcode')->findOrFail($salesContactId);

        $to = $salesContact->email;
        $from = config('mail.from.address');

        $subject = "Unassigned Intro";

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
            "<p>You will soon, or may already have been contacted by our Accredited
                Specialist Design Advisor, Select. Your Design Advisor is your fully accredited
                specialist Spanline Home Additions Design Advisor and as such is qualified to
                provide advice and assistance to you. You are assured that will arrive on time
                at your agreed appointment time and that they will identify themselves by
                producing their personal Spanline photo ID card. These cards are only issued
                to Accredited Spanline Design Advisors under the strictest security.You are
                also assured that will listen to all your requirements and needs and will
                specifically take into account your full wishes before offering any advice or
                ideas for your consideration.</p>" .
            "<p>At Spanline, we believe there should be no secrets or matters not brought out
                into the open, so will explain to you exactly what the Spanline product is, how
                it performs and what you can expect from a Spanline home
                addition.Importantly, will also explain Spanline's exclusive National Customer
                Service Code of Excellence. This code will mean a lot to you should you
                decide to have your home addition project undertaken by Spanline.Naturally
                will also clarify the project financial investment and possible options to ensure
                as best as possible, the project meets the level of investment you are
                anticipating.</p>" .
            "<p>Again we value your interest in Spanline and look forward to providing you
                with the very best of service during your enquiry. If there is anything I can do,
                now or after your design consultation and proposal, please contact me
                immediately.</p>".
            "<p>Thank you again for giving Spanline the opportunity to meet with you and
                advise you on your home addition project.</p><br/> <br/>" .
            "<p>Regards,</p> <br/>" .
            "<div>Spanline Home Additions</div><div>Franchise Manager</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);

        Log::info("Unassigned Intro Letter Sent");

        return response(['status' => 'sent'], Response::HTTP_OK);
    }


}
