<?php


namespace App\Services\Interfaces;


interface EmailServiceInterface
{
    public function sendEmail($email, $message);
}
