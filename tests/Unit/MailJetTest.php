<?php

namespace Tests\Unit;

use App\Services\MailJetService;
use Tests\TestCase;

class MailJetTest extends TestCase
{


    public function testCanSendEmail()
    {

        $mailJet = new MailJetService();

        $response = $mailJet->sendEmail('andyp@crystaltec.com.au', 'Test Message');


    }

}
