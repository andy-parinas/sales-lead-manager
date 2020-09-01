<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;

class MessageMediaService implements Interfaces\SmsServiceInterface
{

    public function sendSms($smsNUmber, $message)
    {

        $url = config('services.sms.url');
        $key = config('services.sms.key');
        $secret = config('services.sms.secret');

        $data =  [
            'messages' => [[
                'content' => $message,
                'destination_number' => $smsNUmber,
                'format' => 'SMS'
            ]]
        ];



        $response = Http::withBasicAuth($key, $secret)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ])->post($url,$data);


        return $response;

    }

}
