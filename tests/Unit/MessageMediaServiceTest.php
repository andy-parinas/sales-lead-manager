<?php

namespace Tests\Unit;

use App\Services\MessageMediaService;
use Tests\TestCase;

class MessageMediaServiceTest extends TestCase
{

    public function testCanSendSMS()
    {

        $messageMedia = new MessageMediaService();

        $response = $messageMedia->sendSms('+61481791820', 'Test From Laravel');

        $this->assertEquals(202, $response->getStatusCode());

    }


}
