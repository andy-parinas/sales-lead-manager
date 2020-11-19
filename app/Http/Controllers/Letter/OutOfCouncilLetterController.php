<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OutOfCouncilLetterController extends Controller
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

        $subject = "Out Of Council";

        $message = "<p> Monday, November 16, 2020 </p> <br/> <br/>" .
            "<div>{$salesContact->title}. {$salesContact->frist_name} {$salesContact->last_name} </div>" .
            "<div>{$salesContact->street1}, {$salesContact->street2}</div>" .
            "<div>{$salesContact->postcode->locality}, {$salesContact->postcode->state}, {$salesContact->postcode->pcode}</div> <br/> <br/>" .
            "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>We are pleased to inform you that your Spanline Home Additions Project has
                been approved. Now that approval has been received we have commenced
                preparing your project for construction.<p>" .
            "<p>We have taken the liberty of outlining the expected works schedule overleaf
                and where necessary noted any special considerations. Please take a
                moment to run through the expected works schedule and if there are any
                points you would like to raise on this schedule, please do not hesitate to call.
                If you are satisfied that everything is in order you do not need to phone, as we
                will contact you to advise of construction commencement details.</p>  <br/>" .
            "<p>Yours faithfully,</p> <br/>" .
            "<div>Project Manager</div><div>Spanline Home Additions</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);

        Log::info("Unassigned Intro Letter Sent");

        return response(['status' => 'sent'], Response::HTTP_OK);
    }
}
