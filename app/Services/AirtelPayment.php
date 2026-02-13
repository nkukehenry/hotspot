<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AirtelPayment implements PaymentService
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.airtel.base_url', env('AIRTEL_BASE_URL'));
        $this->clientId = config('services.airtel.client_id', env('AIRTEL_CLIENT_ID'));
        $this->clientSecret = config('services.airtel.client_secret', env('AIRTEL_CLIENT_SECRET'));
    }

    public function pay($amount, $phone_number, $reference)
    {
        try {
            $accessToken = $this->getAccessToken();
            $formattedPhone = $this->formatPhone($phone_number);
            
            $payload = [
                'reference' => 'CheetahNet Payment',
                'subscriber' => [
                    'country' => 'UG',
                    'currency' => 'UGX',
                    'msisdn' => $formattedPhone
                ],
                'transaction' => [
                    'amount' => (int)$amount,
                    'country' => 'UG',
                    'currency' => 'UGX',
                    'id' => $reference
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
                'X-Country' => 'UG',
                'X-Currency' => 'UGX'
            ])->timeout(30)->post($this->baseUrl . '/merchant/v1/payments/', $payload);

            Log::info("Airtel Payment Response: " . $response->status() . " " . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                 // Airtel returns data.transaction.id
                 $tx_data = $data['data']['transaction'] ?? $data['transaction'] ?? null;
                 $tx_id = $tx_data['id'] ?? $reference;

                 // Start Polling for status
                $startTime = time();
                $timeout = 60; // 60 seconds

                while ((time() - $startTime) < $timeout) {
                    sleep(4); 
                    
                    $statusCheck = $this->checkStatus($tx_id);
                    
                    if ($statusCheck['success']) {
                        $rawStatus = $statusCheck['data']->raw_status;
                        $mappedStatus = $statusCheck['data']->status; // approved, error, pending
                        
                        if ($mappedStatus === 'approved' || $mappedStatus === 'error') {
                            // Populate cache for CustomerController
                            $cbData = [
                                'status' => $mappedStatus,
                                'tid' => $tx_id,
                                'transaction_id' => $tx_id,
                                'raw_status' => $rawStatus,
                                'reason' => $statusCheck['data']->reason ?? null
                            ];
                            Cache::put("callback_{$tx_id}", (object)$cbData, 600);
                            break; 
                        }
                    }
                }
                 
                 return [
                    'success' => true,
                    'data' => (object)[
                        'api_status' => 'success',
                        'tid' => $tx_id,
                        'original_ref' => $reference
                    ]
                 ];
            }

             $errorData = $response->json();
             $errorMessage = $errorData['message'] ?? $errorData['info'] ?? 'Airtel Payment Failed';

             return [
                'success' => false,
                'message' => $errorMessage,
                'data' => (object)['api_status' => 'failed']
            ];

        } catch (\Exception $e) {
            Log::error("Airtel Payment Exception: " . $e->getMessage());
             return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => (object)['api_status' => 'error']
            ];
        }
    }

    public function checkStatus($tranId)
    {
        try {
             $accessToken = $this->getAccessToken();
             
             $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'X-Country' => 'UG',
                'X-Currency' => 'UGX'
            ])->get($this->baseUrl . "/standard/v1/payments/{$tranId}");
            
            Log::info("Airtel Status Response: " . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                $tx_data = $data['data']['transaction'] ?? $data['transaction'] ?? null;
                $status = $tx_data['status'] ?? 'PENDING';
                
                // Map to CustomerController expectations
                // TS = SUCCESSFUL, TF = FAILED
                 $mappedStatus = 'pending';
                if ($status === 'TS' || $status === 'SUCCESSFUL' || $status === 'SUCCESS') $mappedStatus = 'approved';
                if ($status === 'TF' || $status === 'FAILED') $mappedStatus = 'error';

                 return [
                    'success' => true,
                    'data' => (object)[
                        'status' => $mappedStatus,
                        'raw_status' => $status,
                        'reason' => $tx_data['message'] ?? $data['message'] ?? null
                    ]
                ];
            }
             return [
                'success' => false,
                'message' => 'Status check failed',
                'data' => null
            ];

        } catch (\Exception $e) {
             return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    public function processCallback($payload)
    {
         return $payload;
    }

    private function getAccessToken()
    {
        return Cache::remember('airtel_access_token', 3500, function () {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/auth/oauth2/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials'
            ]);
            
            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get Airtel Access Token: ' . $response->body());
        });
    }

    private function formatPhone($phone)
    {
        // Airtel expects number without 256 or 0 prefix?
        // JS says: .replace(/^\+256/, '').replace(/^256/, '').replace(/^0/, '')
        $phone = preg_replace('/\D/', '', $phone);
        if (Str::startsWith($phone, '256')) {
            $phone = substr($phone, 3);
        }
        if (Str::startsWith($phone, '0')) {
            $phone = substr($phone, 1);
        }
        return $phone;
    }
}
