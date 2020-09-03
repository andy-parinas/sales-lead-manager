<?php


namespace App\Services;


use Postmark\PostmarkClient;

class PostmarkService implements Interfaces\EmailServiceInterface
{

    public function sendEmail($email, $message)
    {
        $client = new PostmarkClient("23b169a0-3569-4d48-96a8-37995fa34c1f");
        $fromEmail = "andyp@crystaltec.com.au";
        $toEmail = "andyp@crystaltec.com.au";
        $subject = "Hello from Postmark";
        $htmlBody = "<strong>Hello</strong> dear Postmark user.";
        $textBody = "Hello dear Postmark user.";
        $tag = "example-email-tag";
        $trackOpens = true;
        $trackLinks = "None";
        $messageStream = "outbound";

        // Send an email:
        $sendResult = $client->sendEmail(
            $fromEmail,
            $toEmail,
            $subject,
            $htmlBody,
            $textBody,
            $tag,
            $trackOpens,
            NULL, // Reply To
            NULL, // CC
            NULL, // BCC
            NULL, // Header array
            NULL, // Attachment array
            $trackLinks,
            NULL, // Metadata array
            $messageStream
        );

        dump($sendResult);
    }
}
