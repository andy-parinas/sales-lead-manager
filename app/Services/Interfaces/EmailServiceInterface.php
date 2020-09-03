<?php


namespace App\Services\Interfaces;


interface EmailServiceInterface
{
    public function sendEmail($to, $from, $subject, $message);
}
