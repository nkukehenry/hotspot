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

        $response = $this->paymentService->pay($package->cost, $request->mobileNumber, $transactionId);

        Log::info("Payment Response: " . json_encode($response));

        // Clear the relevant cache after the transaction is recorded
        Cache::forget('reports_data_' . md5(json_encode(request()->all()))); // Clear specific cache for reports
        Cache::forget('package_revenue_data'); // Clear package revenue cache
        Cache::forget('location_revenue_data'); // Clear location revenue cache

        // Send SMS
        $smsService = new SMSService();
        $smsService->sendVoucher($request->mobileNumber, $voucher->code);

        // Redirect to voucher display
        return redirect()->route('customer.voucher', $voucher->code);
    }

    public function showVoucher($voucherCode)
    {
        // Retrieve the voucher associated with the transaction
        $voucher = Voucher::where('code', $voucherCode)->firstOrFail();

        // Check if the voucher is already used
        if ($voucher->is_used) {
            // Find another voucher that is not used and has the same package
            $newVoucher = Voucher::where('package_id', $voucher->package_id)
                ->where('is_used', 0)
                ->first();

            // If a new voucher is found, mark it as used
            if ($newVoucher) {
                $this->markVoucherAsUsed($newVoucher); // Assuming markAsUsed() updates the is_used field
                $voucher = $newVoucher; // Use the new voucher for display
            } else {
                // If no available voucher is found, you can handle this case as needed
                return redirect()->route('customer.packages', $voucher->package->location->code)
                    ->with('error', 'No available vouchers for this package. Contact Admin');
            }
        }

        return view('customer.voucher', compact('voucher'));
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
         Cache::remember("Response",$request->all());
     }
}
