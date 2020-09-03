<?php

namespace Tests\Unit;

use App\Services\PostmarkService;
use Tests\TestCase;

class PostmarkServiceTest extends TestCase
{


    public function testCanSendEmail(){

        $postmark = new PostmarkService();

        $results = $postmark->sendEmail('andyp@crystaltec.com.au', 'andyp@crystaltec.com.au',  'test', 'test');

        $this->assertEquals(0, $results);

    }

}
