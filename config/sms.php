<?php

return [
    "key"=>env("SMS_KEY"),
    "user"=>env("SMS_USER"),
    "whatsapp_device_id" => env("WHATSAPP_DEVICE_ID", 7),
    "whatsapp_email" => env("WHATSAPP_EMAIL"),
    "whatsapp_password" => env("WHATSAPP_PASSWORD")
];