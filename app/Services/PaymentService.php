<?php
namespace App\Services;

interface PaymentService{

    public function pay($amount,$phone_number,$reference);

    function processCallback($payload);

    function checkStatus($tranId);
}