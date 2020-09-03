<?php


namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class MailJetService implements Interfaces\EmailServiceInterface
{

    public function sendEmail($email, $message)
    {
        $mj = new Client('e6ccf994d37470dbbb77972f4195d0f2','c9e8da84ffb166c5182f9a85327c6a86',
            true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "andyp@crystaltec.com.au",
                        'Name' => "Andy"
                    ],
                    'To' => [
                        [
                            'Email' => "andyp@crystaltec.com.au",
                            'Name' => "Andy"
                        ]
                    ],
                    'Subject' => "Greetings from Mailjet.",
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success() && var_dump($response->getData());
    }
}
