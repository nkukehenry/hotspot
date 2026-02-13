<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Package;
use App\Imports\VouchersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\Site;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\LedgerService;

class AdminController extends Controller
{
    protected $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        // 1. Owner Dashboard Data (Platform Wide)
        if ($user->can('view_owner_dashboard')) {
            $baseQuery = Transaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            
            $stats = [
                'total_revenue' => (clone $baseQuery)->sum('amount'),
                'total_vouchers' => Voucher::count(),
                'active_vouchers' => Voucher::where('is_used', false)->count(),
                'total_sites' => Site::count(),
                'recent_transactions' => (clone $baseQuery)->with(['site', 'agent'])->latest()->take(5)->get(),
                'avg_transaction' => (clone $baseQuery)->count() > 0 ? (clone $baseQuery)->sum('amount') / (clone $baseQuery)->count() : 0,
            ];

            $sitePerformance = DB::table('transactions')
                ->join('sites', 'transactions.site_id', '=', 'sites.id')
                ->whereBetween('transactions.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->select('sites.name', DB::raw('sum(amount) as revenue'), DB::raw('count(transactions.id) as sales_count'))
                ->groupBy('sites.id', 'sites.name')
                ->get();

            // Monthly Trend for Chart
            $trendData = DB::table('transactions')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"), DB::raw('sum(amount) as revenue'))
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return view('admin.dashboard.owner', compact('stats', 'sitePerformance', 'trendData', 'dateFrom', 'dateTo'));
        }

        // 2. Manager Dashboard Data (Site Specific)
        if ($user->can('view_manager_dashboard') || $user->can('view_site_dashboard')) {
            $siteId = $user->site_id;
            $baseQuery = Transaction::where('site_id', $siteId)
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            
            $stats = [
                'site_revenue' => (clone $baseQuery)->sum('amount'),
                'available_vouchers' => Voucher::where('site_id', $siteId)->where('is_used', false)->count(),
                'total_agents' => User::role('Agent')->where('site_id', $siteId)->count(),
                'recent_sales' => (clone $baseQuery)->latest()->take(5)->get(),
            ];

            $agentPerformance = DB::table('transactions')
                ->join('users', 'transactions.agent_id', '=', 'users.id')
                ->where('transactions.site_id', $siteId)
                ->whereBetween('transactions.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->select('users.name', DB::raw('sum(amount) as revenue'), DB::raw('count(transactions.id) as sales_count'))
                ->groupBy('users.id', 'users.name')
                ->get();

            $packagePerformance = DB::table('transactions')
                ->join('packages', 'transactions.package_id', '=', 'packages.id')
                ->where('transactions.site_id', $siteId)
                ->whereBetween('transactions.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->select('packages.name', DB::raw('count(transactions.id) as sales_count'))
                ->groupBy('packages.id', 'packages.name')
                ->get();

            return view('admin.dashboard.manager', compact('stats', 'agentPerformance', 'packagePerformance', 'dateFrom', 'dateTo'));
        }

        // Fallback or Basic View
        return view('admin.dashboard');
    }


    public function showReports(Request $request)
    {
        if (!Auth::user()->can('view_reports')) {
            abort(403);
        }

        $user = Auth::user();
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $siteId = $request->input('site_id');
        $packageId = $request->input('package_id');

        // RBAC Scoping
        if ($user->site_id) {
            $siteId = $user->site_id;
        }

        // Base query for summary and table
        $query = DB::table('transactions')
            ->join('vouchers', 'transactions.voucher_id', '=', 'vouchers.id')
            ->join('packages', 'vouchers.package_id', '=', 'packages.id')
            ->join('sites', 'packages.site_id', '=', 'sites.id')
            ->when($siteId, fn($q) => $q->where('sites.id', $siteId))
            ->when($packageId, fn($q) => $q->where('packages.id', $packageId))
            ->when($dateFrom, fn($q) => $q->whereDate('transactions.created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('transactions.created_at', '<=', $dateTo));

        // Sales Data for Table
        $salesData = (clone $query)
            ->select(
                'packages.name as package_name',
                'packages.cost',
                'sites.name as site_name',
                DB::raw('count(transactions.id) as sales_count'),
                DB::raw('sum(packages.cost) as revenue')
            )
            ->groupBy('packages.id', 'packages.name', 'packages.cost', 'sites.id', 'sites.name')
            ->get();

        // Summary Stats
        $summary = [
            'total_revenue' => $salesData->sum('revenue'),
            'total_sales' => $salesData->sum('sales_count'),
            'top_package' => $salesData->sortByDesc('sales_count')->first()?->package_name ?? 'N/A'
        ];

        // Trend Data (Monthly)
        $trendQuery = (clone $query)
            ->select(
                DB::raw("DATE_FORMAT(transactions.created_at, '%Y-%m') as month"),
                DB::raw('sum(packages.cost) as monthly_revenue')
            )
            ->groupBy('month')
            ->orderBy('month', 'ASC');
        
        $trendData = $trendQuery->get();

        // Pie Chart Data
        $packageRevenueData = (clone $query)
            ->select('packages.name as package_name', DB::raw('sum(packages.cost) as total_revenue'))
            ->groupBy('packages.id', 'packages.name')
            ->get();

        $siteRevenueData = (clone $query)
            ->select('sites.name as site_name', DB::raw('sum(packages.cost) as total_revenue'))
            ->groupBy('sites.id', 'sites.name')
            ->get();

        // Dropdowns
        $packages = $user->site_id ? Package::where('site_id', $user->site_id)->get() : Package::all();
        $sites = $user->site_id ? Site::where('id', $user->site_id)->get() : Site::all();

        return view('admin.reports', compact(
            'salesData',
            'packages',
            'sites',
            'packageRevenueData',
            'siteRevenueData',
            'summary',
            'trendData'
        ));
    }

    public function showSettings()
    {
        if (!Auth::user()->can('manage_settings')) {
             abort(403);
        }
        $settings = SystemSetting::first();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::user()->can('manage_settings')) {
             abort(403);
        }

        $request->validate([
            'system_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = SystemSetting::first();
        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
            \Illuminate\Support\Facades\Log::info("System logo updated: " . $logoPath);
        }

        $settings->update($data);
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }

    public function showUploadVouchers()
    {
        if (!Auth::user()->can('create_vouchers')) {
             abort(403);
        }
        
        $query = Package::query();
        if (Auth::user()->site_id) {
            $query->where('site_id', Auth::user()->site_id);
        }
        
        $packages = $query->paginate(10);
        return view('admin.upload_vouchers', compact('packages'));
    }

    public function uploadVouchers(Request $request)
    {
        if (!Auth::user()->can('create_vouchers')) {
             abort(403);
        }

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $packageId = $request->package_id;
        
        // Scope check
        if (Auth::user()->site_id) {
            $package = Package::find($packageId);
            if ($package->site_id != Auth::user()->site_id) {
                abort(403, 'Cannot upload vouchers for another site package.');
            }
        }

        try {
            Excel::import(new VouchersImport($packageId), $request->file('file'));
            return redirect()->route('admin.vouchers')->with('success', 'Vouchers uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsgs = [];
            foreach ($failures as $failure) {
                $errors = $failure->errors();
                $values = $failure->values();
                $value = $values[0] ?? 'N/A';
                
                // Format: Row 5: Voucher is already in the system. (Value: 'ABC-123')
                $errorMsgs[] = "Row {$failure->row()}: " . ($errors[0] ?? 'Validation failed') . " (Value: '{$value}')";
            }
            return redirect()->back()->withErrors($errorMsgs)->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Voucher import failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import vouchers: ' . $e->getMessage())->withInput();
        }
    }

    public function createPackage(Request $request)
    {
        if (!Auth::user()->can('create_packages')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
            'site_id' => 'required|exists:sites,id',
        ]);

        // Scoping check for non-Owners
        if (Auth::user()->site_id && $request->site_id != Auth::user()->site_id) {
             abort(403, 'Cannot create package for another site.');
        }

        Package::create($request->all());
        return redirect()->route('admin.packages')->with('success', 'Package created successfully.');
    }

    
    public function updatePackage(Request $request, Package $package)
    {
        if (!Auth::user()->can('edit_packages')) {
            abort(403);
        }

        // Scoping check
        if (Auth::user()->site_id && $package->site_id != Auth::user()->site_id) {
             abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
            'site_id' => 'required|exists:sites,id',
        ]);

        if (Auth::user()->site_id && $request->site_id != Auth::user()->site_id) {
             abort(403);
        }

        $package->update($request->all());
        return redirect()->route('admin.packages')->with('success', 'Package updated successfully.');
    }


    public function getPackagesBySite($siteId)
    {
        $user = Auth::user();
        if ($user->site_id && $user->site_id != $siteId) {
            abort(403);
        }
        $packages = Package::where('site_id', $siteId)->get();
        return response()->json($packages);
    }

    public function bulkAction(Request $request)
    {
        // Permission check depends on action, assuming delete mostly
        if (!Auth::user()->can('delete_vouchers') && !Auth::user()->can('edit_vouchers')) {
             abort(403);
        }

        $voucherIds = $request->input('voucher_ids', []);
        $status = $request->input('status');

        if ($request->isMethod('post') && !empty($voucherIds)) {
            // Scope check: Ensure all vouchers belong to user's site
            if (Auth::user()->site_id) {
                $count = DB::table('vouchers')
                    ->join('packages', 'vouchers.package_id', '=', 'packages.id')
                    ->whereIn('vouchers.id', $voucherIds)
                    ->where('packages.site_id', Auth::user()->site_id)
                    ->count();
                
                if ($count != count($voucherIds)) {
                    abort(403, 'Unauthorized access to some vouchers.');
                }
            }

            if ($status) {
                // Change status
                 if (!Auth::user()->can('edit_vouchers')) abort(403);
                Voucher::whereIn('id', $voucherIds)->update(['is_used' => $status === 'used']);
            } else {
                // Delete vouchers
                 if (!Auth::user()->can('delete_vouchers')) abort(403);
                Voucher::destroy($voucherIds);
            }
            return redirect()->back()->with('success', 'Bulk action completed.');
        }
        return redirect()->back()->with('error', 'No vouchers selected.');
    }

    public function showPackages(Request $request)
    {
        if (!Auth::user()->can('view_packages')) {
            abort(403);
        }

        $user = Auth::user();
        if ($user->site_id) {
             $sites = Site::where('id', $user->site_id)->get();
        } else {
             $sites = Site::all();
        }

        $siteId = $request->input('site_id');

        // RBAC Override
        if ($user->site_id) {
            $siteId = $user->site_id;
        }

        // Query packages, applying the site filter if provided
        $packages = Package::when($siteId, function ($query, $siteId) {
            return $query->where('site_id', $siteId);
        })->paginate(10);

        return view('admin.packages', compact('packages', 'sites'));
    }

    public function deletePackage(Package $package)
    {
        if (!Auth::user()->can('delete_packages')) {
            abort(403);
        }
         // Scoping check
        if (Auth::user()->site_id && $package->site_id != Auth::user()->site_id) {
             abort(403);
        }

        $package->delete();
        return redirect()->route('admin.packages')->with('success', 'Package deleted successfully.');
    }

    public function showVouchers(Request $request)
    {
        $user = Auth::user();
        if ($user->site_id) {
             $sites = Site::where('id', $user->site_id)->get();
             $packages = Package::where('site_id', $user->site_id)->get();
        } else {
             $sites = Site::all();
             $packages = Package::all();
        }

        $vouchers = DB::table('vouchers')
            ->join('packages', 'vouchers.package_id', '=', 'packages.id')
            ->join('sites', 'packages.site_id', '=', 'sites.id')
            ->select(
                'vouchers.*',
                'packages.name as package_name',
                'packages.cost',
                'sites.name as site_name'
            )
            ->when($request->site_id, function ($query, $siteId) {
                return $query->where('packages.site_id', $siteId);
            })
            ->when($request->package_id, function ($query, $packageId) {
                return $query->where('vouchers.package_id', $packageId);
            })
            ->when($user->site_id, function ($query, $siteId) {
                 return $query->where('packages.site_id', $siteId);
            })
            ->paginate(10);

        return view('admin.vouchers', compact('vouchers', 'sites', 'packages'));
    }
    public function showTransactions(Request $request)
    {
        if (!Auth::user()->can('view_transactions')) {
             abort(403);
        }

        $user = Auth::user();
        if ($user->site_id) {
             $sites = Site::where('id', $user->site_id)->get();
             $packages = Package::where('site_id', $user->site_id)->get();
        } else {
             $sites = Site::all();
             $packages = Package::all();
        }

        $query = Transaction::with(['site', 'voucher.package']);

        // Apply Filters
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('package_id')) {
            $query->whereHas('voucher', function ($q) use ($request) {
                $q->where('package_id', $request->package_id);
            });
        }

        if ($request->filled('mobile_number')) {
            $query->where('mobile_number', 'like', '%' . $request->mobile_number . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // RBAC Scope
        if ($user->site_id) {
            $query->where('site_id', $user->site_id);
        }

        if ($user->hasRole('Agent')) {
            $query->where('agent_id', $user->id);
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.transactions', compact('transactions', 'sites', 'packages'));
    }

    public function showReconciliation()
    {
        if (!Auth::user()->can('view_reports')) { // Reuse view_reports or create specific permission
             abort(403);
        }

        $siteId = Auth::user()->site_id;
        
        // Find all agents for this site
        $agents = User::role('Agent')
            ->where('site_id', $siteId)
            ->get()
            ->map(function($agent) {
                $account = Account::where('code', 'AGENT_CASH_' . $agent->id)->first();
                $agent->cash_balance = $account ? $account->balance : 0;
                return $agent;
            });

        return view('admin.reconciliation', compact('agents'));
    }

    public function reconcileCash(Request $request)
    {
        if (!Auth::user()->can('manage_settings')) { // Reuse for now
             abort(403);
        }

        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $supervisor = Auth::user();
        $agent = User::findOrFail($request->agent_id);
        $amount = $request->amount;

        return DB::transaction(function () use ($agent, $supervisor, $amount) {
            $agentAccount = Account::where('code', 'AGENT_CASH_' . $agent->id)->first();
            
            if (!$agentAccount || $agentAccount->balance < $amount) {
                return back()->with('error', 'Insufficient agent balance.');
            }

            // Site Cash Account
            $siteCashCode = 'SITE_CASH_' . $supervisor->site_id;
            $siteCashAccount = Account::where('code', $siteCashCode)->first();

            if (!$siteCashAccount) {
                $siteCashAccount = Account::create([
                    'name' => 'Site Cash: ' . $supervisor->site->name,
                    'code' => $siteCashCode,
                    'type' => 'asset',
                    'site_id' => $supervisor->site_id,
                    'balance' => 0
                ]);
            }

            // Record Transfer in Ledger
            // DR Site Cash, CR Agent Cash
            $description = "Cash Reconciliation: {$agent->name} -> {$supervisor->name}";
            $this->ledgerService->addEntry($siteCashAccount, $amount, 0, $description);
            $this->ledgerService->addEntry($agentAccount, 0, $amount, $description);

            return back()->with('success', "Successfully reconciled " . number_format($amount) . " from {$agent->name}.");
        });
    }
}
