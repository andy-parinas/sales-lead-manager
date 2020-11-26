<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WelcomeLetterController extends Controller
{
    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
    }


    public function send(Request $request, $leadId)
    {

        $lead = Lead::findOrFail($leadId);

        $salesContact = $lead->salesContact;

        $contract = $lead->contract;

        if($contract == null){
            abort(Response::HTTP_BAD_REQUEST, "Lead must have contract.");
        }

        $to = $salesContact->email;
        $from = config('mail.from.address');

        $subject = "Welcome";

        $message = "<p> Monday, November 16, 2020 </p> <br/> <br/>" .
            "<div>{$salesContact->title}. {$salesContact->frist_name} {$salesContact->last_name} </div>" .
            "<div>{$salesContact->street1}, {$salesContact->street2}</div>" .
            "<div>{$salesContact->postcode->locality}, {$salesContact->postcode->state}, {$salesContact->postcode->pcode}</div> <br/> <br/>" .
            "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>On behalf of our Spanline Home Additions staff, we are honoured that you
                have chosen us to provide you with a unique Spanline Home Addition and
                particularly the \"living pleasure\" that the finished project will bring.<p>" .
            "<p>However, Spanline is about more than just a building project. It is about
                serving, satisfying and fulfilling the expectations of our customers. This we
                look forward to most of all. During the project we will have the opportunity to
                show you just how seriously we take our Code of Customer Service
                Excellence.</p>".
            "<p>We would like to take this opportunity to assure you that we will be doing our
                utmost to make certain everything runs smoothly, but if at any time the
                unexpected does occur, we will keep you fully informed and aware.</p>" .
            "<p>Like all of us, we understand that you are very proud of your home and your
                living environment, and we know that upon completion your Spanline will add
                to that pride. Again we cannot thank you enough for your confidence and we
                would like you to know that every one of us look forward to giving you our
                \"Service Excellence\".</p><br/>".
            "<p>Yours faithfully,</p> <br/>" .
            "<div>Spanline Home Additions</div><div>Franchise Manager</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);

        $contract->update([
            'welcome_letter_sent' => date("Y-m-d")
        ]);

        $contract->refresh();

        Log::info("Welcome Letter Sent {$contract->welcome_letter_sent}");

        return response(['data' => $contract], Response::HTTP_OK);
    }
}
