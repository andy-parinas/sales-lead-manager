<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
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



        Log::info($event->lead->lead_number);
    }
}
