<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Services\FeeService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendSmsJob;
use App\Jobs\SendWhatsAppJob;

class AgentController extends Controller
{
    protected $feeService;
    protected $ledgerService;

    public function __construct(FeeService $feeService, LedgerService $ledgerService)
    {
        $this->feeService = $feeService;
        $this->ledgerService = $ledgerService;
    }

    public function index()
    {
        $agent = Auth::user();
        $site = $agent->site;

        $dailySales = Transaction::where('agent_id', $agent->id)
            ->whereDate('created_at', now())
            ->where('status', 'completed')
            ->sum('amount');

        $dailyCount = Transaction::where('agent_id', $agent->id)
            ->whereDate('created_at', now())
            ->where('status', 'completed')
            ->count();

        $packages = Package::where('site_id', $site->id)->get();
        
        $recentSales = Transaction::with('package')
            ->where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        return view('agent.dashboard', compact('dailySales', 'dailyCount', 'packages', 'recentSales'));
    }

    public function sell(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'mobile_number' => 'nullable|string'
        ]);

        $package = Package::findOrFail($request->package_id);
        $agent = Auth::user();

        return DB::transaction(function () use ($package, $agent, $request) {
            // Find an available voucher
            $voucher = Voucher::where('package_id', $package->id)
                ->where('site_id', $agent->site_id)
                ->where('is_used', false)
                ->lockForUpdate() // Crucial for concurrency
                ->first();

            if (!$voucher) {
                return back()->with('error', 'No vouchers available for this package.');
            }

            // Calculate Fees
            $feeData = $this->feeService->calculateFees($agent->site, $package->cost);

            // Create Transaction (Status: Completed for Agent Sales)
            $transaction = Transaction::create([
                'voucher_id' => $voucher->id,
                'transaction_id' => 'AGT' . strtoupper(uniqid()),
                'mobile_number' => $request->mobile_number ?? 'AGENT-SALE',
                'amount' => $feeData['amount'],
                'customer_fee' => $feeData['customer_fee'],
                'site_fee' => $feeData['site_fee'],
                'total_fee' => $feeData['total_fee'],
                'total_amount' => $feeData['total_amount'],
                'package_id' => $package->id,
                'site_id' => $agent->site_id,
                'agent_id' => $agent->id,
                'status' => 'completed',
            ]);

            // Mark voucher as used
            $voucher->markAsUsed();

            // Record in Ledger
            $this->ledgerService->recordTransaction($transaction);

            // Note: For Agent sales, since it's cash, we may need a separate "Distribute Fees" logic 
            // if fees are deducted from the agent. For now, we'll follow general distribution.
            $this->feeService->distributeFees($transaction);

            // Send notification if mobile number is provided
            if($request->mobile_number) {
                // Dispatch SMS
                SendSmsJob::dispatch($request->mobile_number, $voucher->code);

                // Dispatch WhatsApp
                $whatsappMessage = "Your Wifi Voucher Code is: " . $voucher->code;
                SendWhatsAppJob::dispatch($request->mobile_number, $whatsappMessage);
            }

            return back()->with('success', 'Voucher sold successfully!')
                ->with('voucher_code', $voucher->code);
        });
    }
}
