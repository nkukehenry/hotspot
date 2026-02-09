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

class CustomerController extends Controller
{

    private PaymentService $paymentService;
    private \App\Services\FeeService $feeService;
    private \App\Services\LedgerService $ledgerService;

    function __construct(PaymentService $paymentService, \App\Services\FeeService $feeService, \App\Services\LedgerService $ledgerService){
        $this->paymentService = $paymentService;
        $this->feeService = $feeService;
        $this->ledgerService = $ledgerService;
    }

    public function showSites()
    {
        $sites = Site::all();
        $sites = Site::all();
        return view('customer.sites', compact('sites')); 
    }

    public function showPackages($siteCode)
    {
        $site = Site::where('site_code',$siteCode)->first();
        if(!$site){
             $site = Site::where('slug',$siteCode)->firstOrFail();
        }
        
        $packages = Package::where( 'site_id', $site->id)->get();
        return view('customer.packages', compact('packages', 'site'));
    }

    public function showPayment($packageCode)
    {
        $package = Package::where('code',$packageCode)->firstOrFail();
        return view('customer.payment', compact('package'));
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

        // Check for available voucher
        $voucher = Voucher::where('package_id', $package->id) 
                        ->where('is_used', 0)
                        ->first();
        

        // If no voucher is available, redirect with an error message
        if (!$voucher) {
            return redirect()->route('customer.packages', $package->site->site_code ?? $package->site->slug)
                ->with('error', 'Voucher system temporarily unavailable (Out of Stock).');
        }

        // Set cookie for mobile number
        setcookie('mobile_number', $request->mobileNumber, time() + (86400 * 30), "/"); // 30 days

        // Calculate Fees
        $feeData = $this->feeService->calculateFees($package->site, $package->cost);

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
        ]);

        $response = (Object) $this->paymentService->pay($feeData['total_amount'], $request->mobileNumber, $transactionId);

        Log::info("Payment Response: " . json_encode($response));

        $is_success= 0; //o is pending, 1 is  successful, 2 is failed

        if($response->data && isset($response->data->api_status) && $response->data->api_status =='success' ){

            $transaction->transaction_id = $response->data->tid;
            $transaction->update();

            $transactionId =  $transaction->transaction_id;

            $i=0;

            while($i<10){

                $response =  Cache::get("callback_".$transactionId);

                if($response){

                       $response = (Object) $response;

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
            
            return redirect()->route('customer.payment', $package->code)
                    ->with('error', 'Payment Initiation Failed. Try again.');
        }

        // Clear the relevant cache after the transaction is recorded
        Cache::forget('reports_data'); // Clear specific cache for reports
        Cache::forget('package_revenue_data'); // Clear package revenue cache
        Cache::forget('location_revenue_data'); // Clear location revenue cache needs to be updated to site_revenue_data?

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

            $message = ($is_success==0)?'Payment still pending, please approve to receive a voucher':'Payment Failed. Try again.';

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
         $mobile_number = Cookie::get('mobile_number');
         $transactions = Transaction::where('mobile_number', $mobile_number)->with('voucher')->get();

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

        }
    }

}
