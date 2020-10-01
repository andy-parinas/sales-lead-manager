<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSmsNotification implements ShouldQueue
{

    protected $smsService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     *
     * @param  LeadCreated  $event
     * @return void
     */
    public function handle(LeadCreated $event)
    {

        $lead = $event->lead;
        $salesContact = $event->lead->salesContact;

        $message = "LN:{$event->lead->lead_number} {$salesContact->first_name} {$salesContact->last_name} ";
        $message = $message . "{$salesContact->street1} {$salesContact->postcode->locality}, {$salesContact->postcode->state} ";
        $message = $message . "PH-{$salesContact->contact_number} {$salesContact->email} REQ-{$lead->jobType->product->name}";
        $message = $message . "SRC-{$lead->leadSource->name}";

        $response = $this->smsService->sendSms($lead->jobType->salesStaff->contact_number, $message);

        Log::info("SMS Sent" . $message);

    }
}
