<?php

namespace App\Mail;

use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $lead;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('andyp@crystaltec.com.au', "Andy Parinas")
            ->markdown('emails.lead-created')
            ->with([
                'leadNumber' => $this->lead->lead_number,
                'leadName' => $this->lead->salesContact->full_name,
                'leadContactNumber' => $this->lead->salesContact->contact_numnber,
                'leadEmail' => $this->lead->salesContact->email
            ]);
    }
}
