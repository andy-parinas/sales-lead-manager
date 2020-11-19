<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NoCouncilLetterController extends Controller
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

        $subject = "No Council";

        $message = "<p> Monday, November 16, 2020 </p> <br/> <br/>" .
            "<div>{$salesContact->title}. {$salesContact->frist_name} {$salesContact->last_name} </div>" .
            "<div>{$salesContact->street1}, {$salesContact->street2}</div>" .
            "<div>{$salesContact->postcode->locality}, {$salesContact->postcode->state}, {$salesContact->postcode->pcode}</div> <br/> <br/>" .
            "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>We are pleased to inform you that your Spanline Home Additions project has
                been entered into our check measure program.<p>" .
            "<p>Overleaf we have provided you with some important information relating to
                your project including details of Product and Materials specifications. Please
                take the time to read this information, as it will confirm a number of important
                details about our project.</p>" .
            "<p>If you have any queries, please do not hesitate to contact our Customer
                Service Department. If you are satisfied with all the details there is no need to
                contact us at this point.</p>" .
            "<p>Spanline Home Additions will be contacting you soon with details regarding
                the commencement date of your project.</p> <br/>" .
            "<p>Yours faithfully,</p> <br/>" .
            "<div>Project Manager</div><div>Spanline Home Additions</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);

        Log::info("Unassigned Intro Letter Sent");

        return response(['status' => 'sent'], Response::HTTP_OK);
    }
}
