<?php

namespace App\Services;

use Illuminate\Support\Str;

class SMSService
{
    public function sendVoucher($mobileNumber, $voucherCode)
    {
        $this->sendMessage("Your Wifi Voucher Code is: $voucherCode", $mobileNumber);
    }

    private function sendMessage($msg, $to)
    {
        $apikey = config("sms.key");
        $username = config("sms.user");

        // Format the mobile number
        if (Str::startsWith($to, '256')) {
            $receiver = $to;
        } else {
            $receiver = '256' . substr($to, -9); // Assuming the number is in a local format
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.africastalking.com/version1/messaging',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '?username=' . $username . '&to=' . $receiver . '&message=' . urlencode($msg),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'apiKey:' . $apikey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Optionally log the response or handle errors
        return $response;
    }
}

