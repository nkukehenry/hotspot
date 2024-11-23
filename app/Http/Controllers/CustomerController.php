<?php

namespace App\Http\Controllers;

use App\Models\Location;
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
use App\Jobs\SendSmsJob;

class CustomerController extends Controller
{

    private PaymentService $paymentService;

    function __construct(PaymentService $paymentService){
        $this->paymentService = $paymentService;
    }

    public function showLocations()
    {
        $locations = Location::all();
        return view('customer.locations', compact('locations'));
    }

    public function showPackages($locationCode)
    {
        $location = Location::where('code',$locationCode)->first();
        $packages = Package::where( 'location_id', $location->id)->get();
        return view('customer.packages', compact('packages'));
    }

    public function showPayment($packageCode)
    {
        $package = Package::where('code',$packageCode)->first();
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
        $package = Package::where('code', $packageCode)->first();

        // Check for available voucher
        $voucher = Voucher::where('is_used', 0)->first();

        // If no voucher is available, redirect with an error message
        if (!$voucher) {
            return redirect()->route('customer.packages', $package->location->code)
                ->with('error', 'Voucher system temporarily unavailable.');
        }

        // Set cookie for mobile number
        setcookie('mobile_number', $request->mobileNumber, time() + (86400 * 30), "/"); // 30 days

        // Record the transaction
        $transaction = Transaction::create([
            'voucher_id' => $voucher->id,
            'transaction_id' => $transactionId,
            'mobile_number' => $request->mobileNumber,
            'amount' => $package->cost,
            'package_id' => $package->id,
            'status' => 'completed',
        ]);

        $response = (Object) $this->paymentService->pay($package->cost, $request->mobileNumber, $transactionId);

        Log::info("Payment Response: " . json_encode($response));

        $is_success= 0; //o is pending, 1 is  successful, 2 is failed

        if($response->data && $response->data->api_status =='success' ){

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

            return redirect()->route('customer.payment', $voucher->package->code)
                    ->with('error', 'Payment Initiattion Failed. Try again.');
        }

        // Clear the relevant cache after the transaction is recorded
        Cache::forget('reports_data_' . md5(json_encode(request()->all()))); // Clear specific cache for reports
        Cache::forget('package_revenue_data'); // Clear package revenue cache
        Cache::forget('location_revenue_data'); // Clear location revenue cache

        if($is_success== 1){

        $finalVoucher = $this->getVoucher($voucher);

        if(!$finalVoucher)
            return redirect()->route('customer.payment', $voucher->package->code)
            ->with('error', 'Failure to send voucher for this package. Contact Admin');
        
         $transaction->voucher_id=$finalVoucher->id;
         $transaction->update();
        // Send SMS
        $this->activateAccount($transactionId);
        // Redirect to voucher display
        return redirect()->route('customer.voucher', $voucher->package->code);

        }else{

            
            if($is_success==0){
               
                Cache::remember("callback_".$transactionId."_timedout", 60 * 60, function(){
                    return 10;
                });
            }

            $message = ($is_success==0)?'Payment still pending, please approve to receive a voucher':'Payment Failed. Try again.';

            $message_type = ($is_success==0)?'success':'error';

            return redirect()->route('customer.payment', $voucher->package->code)
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
            // Find another voucher that is not used and has the same package
            $newVoucher = Voucher::where('package_id', $voucher->package_id)
                ->where('is_used', 0)
                ->first();

            // If a new voucher is found, mark it as used
            if ($newVoucher) {
                $this->markVoucherAsUsed($newVoucher); // Assuming markAsUsed() updates the is_used field
                return $newVoucher; // Use the new voucher for display
            } else {
                // If no available voucher is found, you can handle this case as needed
                return false;
            }
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
        $mobileNumber= $transaction->mobile;
        $voucher = Voucher::find($transaction->voucher_id);
        $finalVoucher = $this->getVoucher($voucher);

        if($finalVoucher){
            
            $transaction->voucher_id=$finalVoucher->id;
            $transaction->update();
        
            // Dispatch the job to send SMS
            SendSmsJob::dispatch($mobileNumber, $finalVoucher->code);

        }
    }

}
