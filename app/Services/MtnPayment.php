<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MtnPayment implements PaymentService
{
    private $baseUrl;
    private $subscriptionKey;
    private $clientId;
    private $clientSecret;
    private $environment;
    private $currency;
    private $partyIdType;
    private $callbackUri;
    
    public function __construct()
    {
        $this->baseUrl = config('services.mtn.api_url', env('MTN_API_BASE_URI'));
        if (!Str::endsWith($this->baseUrl, '/')) {
            $this->baseUrl .= '/';
        }
        
        $this->subscriptionKey = config('services.mtn.subscription_key', env('MTN_COLLECTION_SUBSCRIPTION_KEY'));
        $this->clientId = config('services.mtn.user_id', env('MTN_COLLECTION_ID'));
        $this->clientSecret = config('services.mtn.api_secret', env('MTN_COLLECTION_SECRET'));
        $this->environment = config('services.mtn.environment', env('MTN_ENVIRONMENT', 'sandbox'));
        $this->currency = config('services.mtn.currency', env('MTN_CURRENCY', 'UGX'));
        $this->partyIdType = config('services.mtn.party_id_type', env('MTN_COLLECTION_PARTY_ID_TYPE', 'msisdn'));
        $this->callbackUri = config('services.mtn.redirect_uri', env('MTN_COLLECTION_REDIRECT_URI'));
    }

    public function pay($amount, $phone_number, $reference)
    {
        try {
            $accessToken = $this->getAccessToken();
            $momoTransactionId = Str::uuid()->toString();
            
            // Format phone number (256XXXXXXXXX)
            $formattedPhone = $this->formatPhone($phone_number);
            
            $payload = [
                'amount' => (string)$amount,
                'currency' => $this->currency,
                'externalId' => $reference,
                'payer' => [
                    'partyIdType' => $this->partyIdType,
                    'partyId' => $formattedPhone
                ],
                'payerMessage' => 'Internet Subscription',
                'payeeNote' => 'Internet Subscription'
            ];

            $headers = [
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Authorization' => "Bearer {$accessToken}",
                'X-Target-Environment' => $this->environment,
                'X-Reference-Id' => $momoTransactionId,
                'Content-Type' => 'application/json'
            ];
            
            if ($this->callbackUri) {
                // $headers['X-Callback-Url'] = $this->callbackUri; // MTN often ignores this or requires explicit whitelist
            }

            Log::info("MTN Payment Request", [
                'url' => $this->baseUrl . 'collection/v1_0/requesttopay',
                'payload' => $payload,
                'headers_subset' => array_keys($headers)
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . 'collection/v1_0/requesttopay', $payload);

            Log::info("MTN Payment Response: " . $response->status() . " " . $response->body());

            if ($response->status() === 202) {
                // Start Polling for status
                $startTime = time();
                $timeout = 60; // 60 seconds

                while ((time() - $startTime) < $timeout) {
                    sleep(4); 
                    
                    $statusCheck = $this->checkStatus($momoTransactionId);
                    
                    if ($statusCheck['success']) {
                        $rawStatus = $statusCheck['data']->raw_status;
                        $mappedStatus = $statusCheck['data']->status; // approved, error, pending
                        
                        if ($mappedStatus === 'approved' || $mappedStatus === 'error') {
                            // Populate cache for CustomerController
                            $cbData = [
                                'status' => $mappedStatus,
                                'tid' => $momoTransactionId,
                                'transaction_id' => $momoTransactionId,
                                'raw_status' => $rawStatus,
                                'reason' => $statusCheck['data']->reason ?? null
                            ];
                            Cache::put("callback_{$momoTransactionId}", (object)$cbData, 600);
                            break; 
                        }
                    }
                }

                return [
                    'success' => true,
                    'data' => (object)[
                        'api_status' => 'success', 
                        'tid' => $momoTransactionId, 
                        'original_ref' => $reference,
                        'momo_ref' => $momoTransactionId
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => 'MTN Payment Failed: ' . $response->body(),
                'data' => (object)['api_status' => 'failed']
            ];

        } catch (\Exception $e) {
            Log::error("MTN Payment Exception: $this->baseUrl " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => (object)['api_status' => 'error']
            ];
        }
    }

    public function processCallback($payload)
    {
        // Implementation for callback processing if needed directly
        return $payload;
    }

    public function checkStatus($tranId)
    {
        // For MTN, the tranId passed here should be the X-Reference-Id (UUID) used in request
        try {
            $accessToken = $this->getAccessToken();
            
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Authorization' => "Bearer {$accessToken}",
                'X-Target-Environment' => $this->environment,
            ])->get($this->baseUrl . "collection/v1_0/requesttopay/{$tranId}");

            Log::info("MTN Status Response: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status']; // SUCCESSFUL, FAILED, PENDING
                
                // Map to what CustomerController expects: "approved", "closed", "error"
                // Controller check: if($response->status=="approved" || $response->status == "closed")
                
                $mappedStatus = 'pending';
                if ($status === 'SUCCESSFUL') $mappedStatus = 'approved';
                if ($status === 'FAILED') $mappedStatus = 'error';
                
                $rawReason = $data['reason'] ?? null;
                $userReason = $rawReason;

                if ($rawReason === 'LOW_BALANCE_OR_PAYEE_LIMIT_REACHED_OR_NOT_ALLOWED') {
                    $userReason = 'Insufficient funds or limit reached';
                } elseif ($rawReason === 'APPROVAL_REJECTED') {
                    $userReason = 'Payment rejected by user';
                } elseif ($rawReason === 'EXPIRED') {
                    $userReason = 'Payment request timed out';
                }

                return [
                    'success' => true,
                    'data' => (object)[
                        'status' => $mappedStatus,
                        'raw_status' => $status,
                        'reason' => $userReason
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

    private function getAccessToken()
    {
        // Cache token
        return Cache::remember('mtn_access_token', 3500, function () {
            $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");
            
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Authorization' => "Basic {$credentials}",
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . 'collection/token/', [
                'grant_type' => 'client_credentials' // Often sent as body or query depending on implementation, MTN doc says body
            ]); // Wait, axios implementation sends body { grant_type: 'client_credentials' }
            
            // Wait, previous axios implementation:
            /*
             axios.post(
              `${this.baseUrl}collection/token/`,
              { grant_type: 'client_credentials' },
              ...
            */
            // But some MTN implementations behave differently. Let's stick to the JS logic.
            
            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get MTN Access Token: ' . $response->body());
        });
    }

    private function formatPhone($phone)
    {
        // 256XXXXXXXXX
        $phone = preg_replace('/\D/', '', $phone);
        if (Str::startsWith($phone, '0')) {
            $phone = '256' . substr($phone, 1);
        }
        if (Str::startsWith($phone, '256') && strlen($phone) == 12) {
             return $phone;
        }
        // If 9 digits, add 256
         if (strlen($phone) == 9) {
             return '256' . $phone;
        }
        return $phone; // Fallback
    }
}
