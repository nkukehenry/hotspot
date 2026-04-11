<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Package;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Services\JpesaPayment;
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
    private JpesaPayment $jpesaPayment;
    private FeeService $feeService;
    private LedgerService $ledgerService;

    function __construct(MtnPayment $mtnPayment, AirtelPayment $airtelPayment, JpesaPayment $jpesaPayment, FeeService $feeService, LedgerService $ledgerService)
    {
        $this->mtnPayment = $mtnPayment;
        $this->airtelPayment = $airtelPayment;
        $this->jpesaPayment = $jpesaPayment;
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
        $site = Site::where('site_code', $siteCode)->first();
        if (!$site) {
            $site = Site::where('slug', $siteCode)->firstOrFail();
        }

        $packages = Package::where('site_id', $site->id)
            ->whereHas('vouchers', function ($query) {
                $query->where('is_used', 0);
            })->get();
        return view('customer.packages', compact('packages', 'site'));
    }

    public function showPayment($packageCode)
    {
        $package = Package::where('code', $packageCode)->firstOrFail();

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

        // Generate unique transaction ID with timestamp
        $transactionId = 'TXN' . now()->format('ymdHis') . rand(100, 999);

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

        // Provider Selection Logic
        $mobileNumber = $request->mobileNumber;
        $paymentProvider = null;
        $providerName = 'Unknown';

        if (Str::startsWith($mobileNumber, ['070', '075', '074'])) {
            $paymentProvider = $this->airtelPayment;
            $providerName = 'AIRTEL';
        } elseif (Str::startsWith($mobileNumber, ['077', '078', '076'])) {
            $paymentProvider = $this->mtnPayment;
            $providerName = 'MTN';
        } else {
            return redirect()->back()->with('error', 'Unsupported mobile network prefix. Please use MTN (077, 078, 076) or Airtel (070, 075, 074).');
        }

        // Start Transaction
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

        $res = $paymentProvider->pay($feeData['total_amount'], $mobileNumber, $transactionId);

        if ($res['success']) {
            return redirect()->route('customer.waiting', $transactionId);
        }

        return redirect()->back()->with('error', $res['message'] ?? 'Payment initiation failed.');
    }

    public function waiting($transactionId)
    {
        $transaction = Transaction::where('transaction_id', $transactionId)->firstOrFail();
        if ($transaction->status === 'completed') {
            return redirect()->route('customer.voucher', $transaction->id);
        }
        return view('customer.waiting', compact('transaction'));
    }

    public function checkStatus($transactionId)
    {
        $transaction = Transaction::where('transaction_id', $transactionId)->first();
        
        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found']);
        }

        if ($transaction->status === 'completed') {
            return response()->json([
                'status' => 'completed',
                'redirect_url' => route('customer.voucher', $transaction->id)
            ]);
        }

        if ($transaction->status === 'failed') {
            return response()->json(['status' => 'failed', 'message' => 'Payment failed']);
        }

        // Check cache for callback
        $callbackData = Cache::get("callback_" . $transactionId);
        if ($callbackData) {
            $this->activateAccount($transactionId);
            $transaction->refresh();
            if ($transaction->status === 'completed') {
                return response()->json([
                    'status' => 'completed',
                    'redirect_url' => route('customer.voucher', $transaction->id)
                ]);
            }
        }

        return response()->json(['status' => 'pending']);
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

    public function showVoucher($id)
    {
        $transaction = Transaction::findOrFail($id);
        $voucher = $transaction->voucher;
        return view('customer.voucher', compact('voucher', 'transaction'));
    }

    public function handleCallback(Request $request)
    {
        Log::info("Callback Response" . json_encode($request->all()));
        
        $response = (object) $request->all();

        if ($response && ($response->status == 'approved' || $response->status == 'closed' || $response->status == 'SUCCESSFUL')) {
            $transactionId = $response->tid ?? $response->transaction_id ?? $response->externalId ?? null;

            if ($transactionId) {
                Cache::put("callback_" . $transactionId, $response, 600);
                $this->activateAccount($transactionId);
            }
        }

        return response()->json(['status' => 'success']);
    }

    private function activateAccount($transactionId)
    {
        $transaction = Transaction::where('transaction_id', $transactionId)->first();
        if (!$transaction || $transaction->status !== 'pending') {
            return;
        }

        $voucher = Voucher::find($transaction->voucher_id);
        if ($voucher) {
            $voucher->is_used = 1;
            $voucher->save();

            $transaction->status = 'completed';
            $transaction->save();

            // Record Ledger
            $this->ledgerService->recordTransaction($transaction);

            // Distribute Fees
            $this->feeService->distributeFees($transaction);

            // Dispatch Jobs
            SendSmsJob::dispatch($transaction->mobile_number, $voucher->code);
            SendWhatsAppJob::dispatch($transaction->mobile_number, "Your WiFi voucher code is: " . $voucher->code);
        }
    }
}
