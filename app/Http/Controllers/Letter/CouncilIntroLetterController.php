<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CouncilIntroLetterController extends Controller
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

        $buildingAuthority = $lead->buildingAuthority;

        if($buildingAuthority == null){
            abort(Response::HTTP_BAD_REQUEST, "Building Authority Is Required");
        }

        $to = $salesContact->email;
        $from = config('mail.from.address');

        $subject = "Council Intro";

        $message = "<p> Monday, November 16, 2020 </p> <br/> <br/>" .
            "<div>{$salesContact->title}. {$salesContact->frist_name} {$salesContact->last_name} </div>" .
            "<div>{$salesContact->street1}, {$salesContact->street2}</div>" .
            "<div>{$salesContact->postcode->locality}, {$salesContact->postcode->state}, {$salesContact->postcode->pcode}</div> <br/> <br/>" .
            "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>We are delighted to advise you that your Spanline Home Addition project has
                now been submitted for statutory approval and we enclose a copy of the plans for
                your information.<p>" .
            "<p>While we do not foresee any problems, occasionally the council application
                requires additional processes, therefore delays do occur. Should this happen,
                we will be in contact with you as soon as possible.</p>".
            "<p>Overleaf we have provided you with some important information relating to
                your project including details of the Product and Materials specification.
                Please take the time to read this information, as it will confirm a number of details
                about your project. If you have any queries at all please contact our Customer
                Service Department.</p>" .
            "<p>If you are having concreting or any other structural work completed prior to
                your Spanline project starting, please advise us once the work is complete, as
                your project may need to be re-measured.</p>".
            "<p>We look forward to contacting you soon to advise that your project has been
                approved and providing you with material delivery and works schedule details.</p><br/> <br/>" .
            "<p>Yours faithfully,</p> <br/>" .
            "<div>Project Manager</div><div>Spanline Home Additions</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);


        $buildingAuthority->update([
            'intro_council_letter_sent' => date("Y-m-d")
        ]);

        $buildingAuthority->refresh();

        Log::info("Unassigned Intro Letter Sent");

        return response(['data' => $buildingAuthority], Response::HTTP_OK);
    }
}
