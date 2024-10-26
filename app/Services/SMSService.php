<?php

namespace App\Services;

class SMSService
{
    public function sendVoucher($mobileNumber, $voucherCode)
    {
        // Integrate with an SMS API like Twilio
        // Example: Twilio::message($mobileNumber, "Your voucher code is: $voucherCode");

        // For now, we'll simulate sending an SMS
        return true;
    }
}

