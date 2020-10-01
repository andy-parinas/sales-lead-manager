<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\Interfaces\EmailServiceInterface;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadCreatedMail;

class SendEmailNotification implements ShouldQueue
{

    protected $mailService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(EmailServiceInterface $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Handle the event.
     *
     * @param  LeadCreated  $event
     * @return void
     */
    public function handle(LeadCreated $event)
    {

        $to = $event->lead->jobType->salesStaff->email;
        $from = config('mail.from.address');

        $subject = "New Sales Lead Assigned: {$event->lead->lead_number}";

        $message = "<h1>A new Sales Lead has been assigned to you</h1>" .
                    "<p>Lead Number: <strong>{$event->lead->lead_number}</strong> </p>" .
                    "<p>Name: <strong>{$event->lead->salesContact->full_name}</strong></p>" .
                    "<p>Contact Number: <strong>{$event->lead->salesContact->contact_number}</strong></p>" .
                    "<p>Email: <strong>{$event->lead->salesContact->email}</strong></p>" ;

        $this->mailService->sendEmail($to, $from, $subject, $message);

        Log::info("Email Notification Sent");

    }
}
