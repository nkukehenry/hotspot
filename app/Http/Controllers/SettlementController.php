<?php

namespace App\Http\Controllers;

use App\Models\SettlementRequest;
use App\Models\Site;
use App\Models\Transaction;
use App\Services\LedgerService; // Assuming you have this service based on context
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    protected $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = SettlementRequest::with('site', 'approver')->withSum('transactions', 'site_fee')->latest();

        if ($user->site_id) {
            $query->where('site_id', $user->site_id);
            $site = Site::find($user->site_id);
            // Verify balance dynamically
            $availableBalance = Transaction::where('site_id', $user->site_id)
                ->whereNull('agent_id')
                ->whereNull('settlement_request_id')
                ->sum(DB::raw('amount - site_fee'));

            $pendingFees = Transaction::where('site_id', $user->site_id)
                ->whereNull('agent_id')
                ->whereNull('settlement_request_id')
                ->sum('site_fee');
        } else {
            if ($request->site_id) {
                $query->where('site_id', $request->site_id);
                $site = Site::find($request->site_id);
                $availableBalance = Transaction::where('site_id', $request->site_id)
                    ->whereNull('agent_id')
                    ->whereNull('settlement_request_id')
                    ->sum(DB::raw('amount - site_fee'));

                $pendingFees = Transaction::where('site_id', $request->site_id)
                    ->whereNull('agent_id')
                    ->whereNull('settlement_request_id')
                    ->sum('site_fee');
            } else {
                $site = null;
                $availableBalance = 0;
                $pendingFees = 0;
            }
        }

        $settlements = $query->paginate(15);
        $sites = Site::all();

        return view('admin.settlements.index', compact('settlements', 'availableBalance', 'pendingFees', 'sites', 'site'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->site_id && !$request->site_id) {
             if (!Auth::user()->can('manage_sites')) abort(403, 'Must select a site');
        }
        
        $siteId = $user->site_id ?? $request->site_id;
        $site = Site::findOrFail($siteId);

        // Calculate Total Available Balance (Unsettled Digital Sales - Merchant Fees)
        $availableBalance = Transaction::where('site_id', $siteId)
            ->whereNull('agent_id')
            ->whereNull('settlement_request_id')
            ->sum(DB::raw('amount - site_fee'));

        return view('admin.settlements.create', compact('site', 'availableBalance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user();
        if ($user->site_id && $user->site_id != $request->site_id) {
            abort(403);
        }

        DB::transaction(function () use ($request) {
            // Lock rows for reading
            $query = Transaction::where('site_id', $request->site_id)
                ->whereNull('agent_id')
                ->whereNull('settlement_request_id')
                ->orderBy('created_at', 'asc') // FIFO
                ->lockForUpdate(); 
            
            $allTransactions = $query->get();
            // Calculate Total Available based on Net Amount (Amount - Site Fee)
            $totalAvailable = $allTransactions->sum(function($tx) {
                return $tx->amount - $tx->site_fee;
            });

            if ($request->amount > $totalAvailable) {
                 abort(422, 'Requested amount exceeds available balance.');
            }

            // Allocate Transactions to meet the requested amount
            $currentSum = 0;
            $allocatedTxIds = [];

            foreach ($allTransactions as $tx) {
                $netAmount = $tx->amount - $tx->site_fee;
                
                if ($currentSum + $netAmount <= $request->amount) {
                    $currentSum += $netAmount;
                    $allocatedTxIds[] = $tx->id;
                } else {
                    // Cannot fit this transaction without exceeding amount. 
                    // Stop here (This may result in slightly less than requested)
                    break; 
                }
            }

            if ($currentSum <= 0) {
                 // Could happen if requested amount is smaller than the smallest transaction's net value
                 // For now, allow it to proceed effectively as a 0 request or handle error?
                 // Let's abort if 0 is allocated to prevent empty requests
                 // But validation said min:0.01.
                 // If we can't match any transaction, we should probably error.
                 if(count($allocatedTxIds) == 0) {
                     abort(422, 'Cannot allocate any transactions for this amount (smallest transaction exceeds request).');
                 }
            }

            $settlement = SettlementRequest::create([
                'site_id' => $request->site_id,
                'amount' => $currentSum,
                'start_date' => now(), 
                'end_date' => now(),
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Lock allocated transactions
            Transaction::whereIn('id', $allocatedTxIds)->update(['settlement_request_id' => $settlement->id]);
        });

        return redirect()->route('admin.settlements.index')->with('success', 'Withdrawal request submitted successfully.');
    }

    public function approve(SettlementRequest $settlement)
    {
        if (!Auth::user()->can('manage_sites')) { // Assuming generic admin permission
             abort(403);
        }

        if ($settlement->status !== 'pending') {
            return back()->with('error', 'Request already processed.');
        }

        DB::transaction(function () use ($settlement) {
            $settlement->status = 'approved';
            $settlement->approved_by = Auth::id();
            $settlement->save();

            // Update Site Balance
            $site = $settlement->site;
            $site->decrement('digital_sales_balance', $settlement->amount);

            // Ledger Entry (Optional / If LedgerService exists)
            // Debit: Site Payable (Liability decreases)
            // Credit: Bank/Cash (Asset decreases)
            
            // Note: I am not calling LedgerService here yet as I need to be sure about Account codes. 
            // In a full implementation, we'd resolve the Site Payable Account and System Bank Account.
        });

        return back()->with('success', 'Settlement approved.');
    }
}
