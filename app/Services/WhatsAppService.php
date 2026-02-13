<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppService
{
    private $loginUrl = 'https://api.bulkoms.com/api/v1/auth/login';
    private $sendUrl = 'https://api.bulkoms.com/api/v1/messages/send';
    private $cacheKey = 'bulkoms_whatsapp_token';

    /**
     * Send a WhatsApp message
     *
     * @param string $to Recipient phone number
     * @param string $message Message content
     * @return mixed Response object or false on failure
     */
    public function sendMessage($to, $message)
    {
        $deviceId = config("sms.whatsapp_device_id");
        
        // Format the mobile number to 256 format
        $receiver = substr($to, 0, 1) === '0' ? '256' . substr($to, 1) : $to;

        // Get authentication token
        $token = $this->getToken();
        
        if (!$token) {
            Log::error("WhatsApp Auth Failed: Could not retrieve token.");
            return false;
        }

        $payload = [
            'to' => $receiver,
            'message' => $message,
            'deviceId' => intval($deviceId)
        ];

        Log::info("Sending WhatsApp to: " . $receiver . " via Device ID: " . $deviceId);
        Log::info("WhatsApp Payload: " . json_encode($payload));
        $response = $this->makeRequest($this->sendUrl, 'POST', $payload, $token);
        
        // Handle token expiration/invalid token scenario
        if ($response && isset($response->statusCode) && $response->statusCode === 401) {
            Log::warning("WhatsApp Token Expired. Refreshing token and retrying...");
            Cache::forget($this->cacheKey);
            $token = $this->getToken();
            if ($token) {
                $response = $this->makeRequest($this->sendUrl, 'POST', $payload, $token);
            }
        }

         Log::info("WhatsApp Response: " . json_encode($response));

        return $response;
    }

    /**
     * Get a valid JWT token, either from cache or by logging in
     */
    private function getToken()
    {
        $token = Cache::get($this->cacheKey);
        
        if ($token) {
            return $token;
        }

        $token = $this->login();

        if ($token) {
            // Cache for 24 hours (or slightly less than token expiry)
            Cache::put($this->cacheKey, $token, 60 * 60 * 24);
        }

        return $token;
    }

    /**
     * Authenticate with the API and return the token
     */
    private function login()
    {
         $email = config('sms.whatsapp_email');
         $password = config('sms.whatsapp_password');
         
         if (!$email || !$password) {
             Log::error("WhatsApp Configuration Missing: Email or Password not set in .env file.");
             return null;
         }

         $payload = [
             'email' => $email,
             'password' => $password
         ];

         // Pass null as token to avoid loop
         $response = $this->makeRequest($this->loginUrl, 'POST', $payload, null);

         if ($response && isset($response->success) && $response->success && isset($response->token)) {
             Log::info("WhatsApp Login Successful. Token obtained.");
             return $response->token;
         }

         Log::error("WhatsApp Login Failed: " . json_encode($response));
         return null;
    }

    /**
     * Helper to make cURL requests
     */
    private function makeRequest($url, $method = 'GET', $data = [], $token = null)
    {
        $curl = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers
        ];

        if ($method === 'POST' || $method === 'PUT') {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error("WhatsApp cURL Error: " . $err);
            return null;
        }

        return json_decode($response);
    }
}
