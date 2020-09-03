<?php

namespace Tests\Unit;

use App\Services\PostmarkService;
use Tests\TestCase;

class PostmarkServiceTest extends TestCase
{


    public function testCanSendEmail(){

        $postmark = new PostmarkService();

        $postmark->sendEmail('andyp@crystaltec.com.au', 'test');

    }

}
