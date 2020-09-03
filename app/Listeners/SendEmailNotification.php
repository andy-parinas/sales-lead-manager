<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadCreatedMail;

class SendEmailNotification implements ShouldQueue
{


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  LeadCreated  $event
     * @return void
     */
    public function handle(LeadCreated $event)
    {
//        Mail::to($event->lead->jobType->salesStaff->email)->send(new LeadCreatedMail($event->lead));

        $data = [
            'leadNumber' => $event->lead->lead_number,
            'leadName' => $event->lead->salesContact->full_name,
            'leadContactNumber' => $event->lead->salesContact->contact_numnber,
            'leadEmail' => $event->lead->salesContact->email
        ];

        Mail::send('emails.lead-created', $data, function ($message) use ($event) {
            $message->from('andyp@crystaltec.com.au', 'Andy Parinas');
            $message->to($event->lead->jobType->salesStaff->email);
            $message->subject("Test Message");
        });
        Log::info("Email Notification 2");
        Log::info($event->lead->jobType->salesStaff->email);

    }
}
