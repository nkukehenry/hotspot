<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Package;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Services\SMSService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendSmsJob;
use App\Jobs\SendWhatsAppJob;
use App\Services\AirtelPayment;
use App\Services\MtnPayment;
use App\Services\FeeService;
use App\Services\LedgerService;



class CustomerController extends Controller
{

    private MtnPayment $mtnPayment;
    private AirtelPayment $airtelPayment;
    private FeeService $feeService;
    private LedgerService $ledgerService;

    function __construct(MtnPayment $mtnPayment, AirtelPayment $airtelPayment, FeeService $feeService, LedgerService $ledgerService){
        $this->mtnPayment = $mtnPayment;
        $this->airtelPayment = $airtelPayment;
        $this->feeService = $feeService;
        $this->ledgerService = $ledgerService;
    }

    public function showSites()
    {
        $sites = Site::all();
        $settings = \App\Models\SystemSetting::first();
        return view('customer.sites', compact('sites', 'settings')); 
    }

    public function showPackages($siteCode)
    {
        $site = Site::where('site_code',$siteCode)->first();
        if(!$site){
             $site = Site::where('slug',$siteCode)->firstOrFail();
        }
        
        $packages = Package::where('site_id', $site->id)
            ->whereHas('vouchers', function ($query) {
                $query->where('is_used', 0);
            })->get();
        return view('customer.packages', compact('packages', 'site'));
    }

    public function showPayment($packageCode)
    {
        $package = Package::where('code',$packageCode)->firstOrFail();
        
        // Re-verify availability before showing payment page
        $hasVouchers = Voucher::where('package_id', $package->id) 
                        ->where('is_used', 0)
                        ->exists();

        if (!$hasVouchers) {
             return redirect()->route('customer.packages', $package->site->slug ?? $package->site->site_code)
                ->with('error', 'Sorry, this package just went out of stock.');
        }

        $site = $package->site;
        return view('customer.payment', compact('package', 'site'));
    }

    public function processPayment(Request $request, $packageCode)
    {
        // Validate the request
        $request->validate([
            'mobileNumber' => 'required|numeric',
        ]);

        // Simulate payment processing
        $transactionId = 'TXN' . rand(1000, 9999);

        // Retrieve the package
        $package = Package::where('code', $packageCode)->firstOrFail();

        // Check for available voucher BEFORE anything else
        $voucher = Voucher::where('package_id', $package->id) 
                        ->where('is_used', 0)
                        ->first();
        
        // If no voucher is available, redirect with an error message
        if (!$voucher) {
            return redirect()->route('customer.packages', $package->site->slug ?? $package->site->site_code)
                ->with('error', 'Voucher system temporarily unavailable (Out of Stock).');
        }

        // Set cookie for mobile number (30 days)
        Cookie::queue('mobile_number', $request->mobileNumber, 43200);

        // Calculate Fees
        $feeData = $this->feeService->calculateFees($package->site, $package->cost);

        $mobileNumber = $request->mobileNumber;
        $paymentProvider = null;
        $providerName = 'Unknown';

        // Provider Selection Logic
        if (Str::startsWith($mobileNumber, ['070', '075', '074'])) {
            $paymentProvider = $this->airtelPayment;
            $providerName = 'AIRTEL';
        } elseif (Str::startsWith($mobileNumber, ['077', '078', '076'])) {
            $paymentProvider = $this->mtnPayment;
            $providerName = 'MTN';
        } else {
            return redirect()->back()->with('error', 'Unsupported mobile network prefix. Please use MTN (077, 078, 076) or Airtel (070, 075, 074).');
        }

        // Record the transaction
        $transaction = Transaction::create([
            'voucher_id' => $voucher->id,
            'transaction_id' => $transactionId,
            'mobile_number' => $request->mobileNumber,
            'amount' => $feeData['amount'],
            'customer_fee' => $feeData['customer_fee'],
            'site_fee' => $feeData['site_fee'],
            'total_fee' => $feeData['total_fee'],
            'total_amount' => $feeData['total_amount'],
            'package_id' => $package->id,
            'site_id' => $package->site_id,
            'agent_id' => Auth::check() ? Auth::id() : null,
            'status' => 'pending', 
            // We might want to store provider name if we add a column later, but for now Transaction model doesn't have it explicitly in fillable given the previous views.
        ]);

        // Update transaction history cookie AFTER creation using the immutable PK ID
        $history = json_decode(Cookie::get('transaction_history') ?? $_COOKIE['transaction_history'] ?? '[]', true);
        if (!is_array($history)) $history = [];
        $history[] = $transaction->id;
        // Keep only last 50 transactions
        $history = array_slice($history, -50); 
        Cookie::queue('transaction_history', json_encode($history), 43200);

        $response = (Object) $paymentProvider->pay($feeData['total_amount'], $request->mobileNumber, $transactionId);

        Log::info("Payment Response: " . json_encode($response));

        $is_success= 0; //o is pending, 1 is  successful, 2 is failed

        if($response->data && isset($response->data->api_status) && $response->data->api_status =='success' ){

            $transaction->transaction_id = $response->data->tid;
            $transaction->update();

            $transactionId =  $transaction->transaction_id;

            $i=0;

            $payment_reason = null;

            while($i<10){

                $response =  Cache::get("callback_".$transactionId);

                if($response){

                       $response = (Object) $response;
                       $payment_reason = $response->reason ?? null;

                      if($response->status=="approved"  || $response->status == "closed"){
                        $is_success= 1;
                       }
                       else if($response->status=="error"){
                        $is_success= 2;
                       }
                       else{
                           $is_success = 0;
                       }
   
                       break;
                }

                $i++;

                sleep(3);
            }

        }else{
            
            $transaction->status = 'failed';
            $transaction->save();
            
            $errorMessage = $response->message ?? 'Payment Initiation Failed. Try again.';

            return redirect()->route('customer.payment', $package->code)
                    ->with('error', $errorMessage);
        }

        // Clear the relevant cache after the transaction is recorded
        Cache::forget('reports_data'); // Clear specific cache for reports
        Cache::forget('package_revenue_data'); // Clear package revenue cache
        Cache::forget('location_revenue_data'); // Clear location revenue cache needs to be updated to site_revenue_data?

        $is_success = 1;

        if($is_success== 1){

        $finalVoucher = $this->getVoucher($voucher);

        if(!$finalVoucher){
            $transaction->status = 'paid_no_voucher';
            $transaction->save();
            return redirect()->route('customer.payment', $package->code)
            ->with('error', 'Payment received but failure to assign voucher. Contact Admin');
        }
        
         $transaction->voucher_id=$finalVoucher->id;
         $transaction->status = 'completed';
         $transaction->update();
        // Send SMS
        $this->activateAccount($transactionId);
        // Redirect to voucher display
        return redirect()->route('customer.voucher', $finalVoucher->code);

        }else{

            
            if($is_success==0){
               
                Cache::remember("callback_".$transactionId."_timedout", 60 * 60, function(){
                    return 10;
                });
            }

            $message = ($is_success==0)?'Payment still pending, please approve to receive a voucher':'Payment Failed. ' . ($payment_reason ?? 'Try again.');

            $message_type = ($is_success==0)?'success':'error';

            return redirect()->route('customer.payment', $package->code)
            ->with($message_type,$message );
        }
    }

    public function showVoucher($voucherCode)
    {
        // Retrieve the voucher associated with the transaction
        $voucher = Voucher::where('code', $voucherCode)->firstOrFail();
        return view('customer.voucher', compact('voucher'));
    }

    public function getVoucher($voucher){

          $voucher = Voucher::find($voucher->id);
           // Check if the voucher is already used
           if ($voucher->is_used) {
            // Find another voucher that is not used and has the same package AND SITE
            $newVoucher = Voucher::where('package_id', $voucher->package_id)
                ->where('site_id', $voucher->site_id)
                ->where('is_used', 0)
                ->first();

            // If a new voucher is found, mark it as used
            if ($newVoucher) {
                $this->markVoucherAsUsed($newVoucher->id); 
                return $newVoucher; // Use the new voucher for display
            } else {
                // If no available voucher is found, you can handle this case as needed
                return false;
            }
        } else {
            $this->markVoucherAsUsed($voucher->id);
        }

        return $voucher;
    }

    // Example usage
    public function markVoucherAsUsed($voucherId)
    {
        $voucher = Voucher::find($voucherId);
        $voucher->markAsUsed();
    }

    public function showTransactions()
     { 
         // Retrieve transaction IDs from cookie
         $history = json_decode(Cookie::get('transaction_history') ?? $_COOKIE['transaction_history'] ?? '[]', true);
         
         if (empty($history) || !is_array($history)) {
             $transactions = collect();
         } else {
             // Query by ID (primary key) for stability
             $transactions = Transaction::whereIn('id', $history)
                                        ->with(['voucher', 'package'])
                                        ->orderBy('created_at', 'desc')
                                        ->get();
         }

         return view('customer.transactions', compact('transactions'));
     }

     public function handleCallback(Request $request)
     {
         Log::info("Callback Response".json_encode($request->all()));
        
         $response = (Object) $request->all();

         if($response && ($response->status =='approved' || $response->status =='closed') ){

            $transactionId = $response->tid;

            Cache::remember("callback_".$transactionId, 60 * 60, function() use ($request) {
                    return $request->all();
            });

            $timed_out = Cache::get("callback_".$transactionId."_timedout");
            Log::info("Timed Out:: ".$timed_out);

            if($timed_out)
             $this->activateAccount($transactionId);

        }

        return 'success';

    }

    private function activateAccount($transactionId){


        $transaction = Transaction::where('transaction_id',$transactionId)->first();
        if(!$transaction) return;
        
        $mobileNumber= $transaction->mobile_number;
        $voucher = Voucher::find($transaction->voucher_id);
        
        $finalVoucher = $this->getVoucher($voucher);

        if($finalVoucher){
            
            $transaction->voucher_id=$finalVoucher->id;
            $transaction->status = 'completed';
            $transaction->update();
            
            // Record in Ledger
            $this->ledgerService->recordTransaction($transaction);

            // Distribute Fees
            $this->feeService->distributeFees($transaction);
        
            // Dispatch the job to send SMS
            SendSmsJob::dispatch($mobileNumber, $finalVoucher->code);
            
            // Dispatch the job to send WhatsApp
            $whatsappMessage = "Your Wifi Voucher Code is: " . $finalVoucher->code;
            SendWhatsAppJob::dispatch($mobileNumber, $whatsappMessage);

        }
    }

}
