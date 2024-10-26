<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Package;
use App\Models\Voucher;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\SMSService;
use Illuminate\Support\Facades\Cookie;
class CustomerController extends Controller
{
    public function showLocations()
    {
        $locations = Location::all();
        return view('customer.locations', compact('locations'));
    }

    public function showPackages($locationId)
    {
        $packages = Package::where('location_id', $locationId)->get();
        return view('customer.packages', compact('packages'));
    }

    public function showPayment($packageId)
    {
        $package = Package::findOrFail($packageId);
        return view('customer.payment', compact('package'));
    }

    public function processPayment(Request $request, $packageId)
    {
        // Here you would integrate with a payment service
        // For now, we'll just simulate a successful payment

        // Validate the request
        $request->validate([
            'mobileNumber' => 'required|numeric',
        ]);

        // Simulate payment processing
        $transactionId = 'TXN' . rand(1000, 9999);

        // Simulate voucher creation
        $voucher = Voucher::where('is_used',0)->first();
        $package = Package::findOrFail($packageId);

      //  dd($package);

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

        // Send SMS
        $smsService = new SMSService();
        $smsService->sendVoucher($request->mobileNumber, $voucher->code);

        // Redirect to voucher display
        return redirect()->route('customer.voucher', $voucher->id);
    }

    public function showVoucher($voucherId)
    {
        // Retrieve the voucher associated with the transaction
        // For simplicity, we'll assume a direct relationship
        $voucher = Voucher::where('id', $voucherId)->firstOrFail();

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
}
