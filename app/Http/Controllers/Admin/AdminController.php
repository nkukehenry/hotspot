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
use App\Models\Location;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch sales data for the dashboard
        $salesData = DB::table('transactions')
            ->join('vouchers', 'transactions.voucher_id', '=', 'vouchers.id')
            ->join('packages', 'vouchers.package_id', '=', 'packages.id')
            ->join('locations', 'packages.location_id', '=', 'locations.id')
            ->select(
                'packages.name as package_name',
                'locations.name as location_name',
                DB::raw('count(transactions.id) as sales_count'),
                DB::raw('sum(packages.cost) as revenue')
            )
            ->groupBy('packages.id', 'packages.name', 'locations.id', 'locations.name')
            ->get();

        return view('admin.dashboard', compact('salesData'));
    }

    public function showUsers()
    {
        $users = User::with('roles')->paginate();
        return view('admin.users', compact('users'));
    }

    public function editUser(User $user)
    {
        // Logic to edit user roles
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function showReports(Request $request)
    {
        // Define a unique cache key based on the request parameters
        $cacheKey = 'reports_data_' . md5(json_encode($request->all()));

        // Attempt to retrieve the cached data
        $salesData = Cache::remember($cacheKey, 60 * 60, function () use ($request) {
            // Initialize the query for sales data
            $query = DB::table('transactions')
                ->join('vouchers', 'transactions.voucher_id', '=', 'vouchers.id')
                ->join('packages', 'vouchers.package_id', '=', 'packages.id')
                ->join('locations', 'packages.location_id', '=', 'locations.id')
                ->select(
                    'packages.name as package_name',
                    'packages.cost',
                    'locations.name as location_name',
                    DB::raw('count(transactions.id) as sales_count'),
                    DB::raw('sum(packages.cost) as revenue')
                );

            // Apply filters if provided
            if ($request->filled('package_id')) {
                $query->where('packages.id', $request->package_id);
            }

            if ($request->filled('location_id')) {
                $query->where('locations.id', $request->location_id);
            }

            // Group the results
            return $query->groupBy('packages.id', 'packages.name', 'packages.cost', 'locations.id', 'locations.name')->get();
        });

        // Prepare data for pie charts with the same filters applied
        $packageRevenueData = Cache::remember('package_revenue_data_' . $cacheKey, 60 * 60, function () use ($request) {
            $query = DB::table('transactions')
                ->join('vouchers', 'transactions.voucher_id', '=', 'vouchers.id')
                ->join('packages', 'vouchers.package_id', '=', 'packages.id')
                ->join('locations', 'packages.location_id', '=', 'locations.id')
                ->select('packages.name as package_name', DB::raw('sum(packages.cost) as total_revenue'));

            // Apply filters if provided
            if ($request->filled('package_id')) {
                $query->where('packages.id', $request->package_id);
            }

            if ($request->filled('location_id')) {
                $query->where('locations.id', $request->location_id);
            }

            return $query->groupBy('packages.id', 'packages.name')->get();
        });

        $locationRevenueData = Cache::remember('location_revenue_data_' . $cacheKey, 60 * 60, function () use ($request) {
            $query = DB::table('transactions')
                ->join('vouchers', 'transactions.voucher_id', '=', 'vouchers.id')
                ->join('packages', 'vouchers.package_id', '=', 'packages.id')
                ->join('locations', 'packages.location_id', '=', 'locations.id')
                ->select('locations.name as location_name', DB::raw('sum(packages.cost) as total_revenue'));

            // Apply filters if provided
            if ($request->filled('package_id')) {
                $query->where('packages.id', $request->package_id);
            }

            if ($request->filled('location_id')) {
                $query->where('locations.id', $request->location_id);
            }

            return $query->groupBy('locations.id', 'locations.name')->get();
        });

        // Fetch all packages and locations for the filter dropdowns
        $packages = Package::all();
        $locations = Location::all();

        // Return the view with the data
        return view('admin.reports', compact('salesData', 'packages', 'locations', 'packageRevenueData', 'locationRevenueData'));
    }

    public function showSettings()
    {
        $settings = SystemSetting::first();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = SystemSetting::first();
        $settings->update($request->all());
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }

    public function showUploadVouchers()
    {
        $packages = Package::paginate(10);
        return view('admin.upload_vouchers', compact('packages'));
    }

    public function uploadVouchers(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $packageId = $request->package_id;

        Excel::import(new VouchersImport($packageId), $request->file('file'));

        return redirect()->route('admin.vouchers')->with('success', 'Vouchers uploaded successfully.');
    }

    public function showLocations()
    {
        $locations = Location::paginate(10);
        return view('admin.locations', compact('locations'));
    }

    public function createLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        Location::create($request->only(['name', 'address']));

        return redirect()->route('admin.locations')->with('success', 'Location created successfully.');
    }

    public function updateLocation(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $location->update($request->only(['name', 'address']));

        return redirect()->route('admin.locations')->with('success', 'Location updated successfully.');
    }

    public function deleteLocation(Location $location)
    {
        $location->delete();
        return redirect()->route('admin.locations')->with('success', 'Location deleted successfully.');
    }

    public function createPackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
        ]);

        Package::create($request->all());
        return redirect()->route('admin.packages')->with('success', 'Package created successfully.');
    }

    
    public function updatePackage(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
        ]);

        $package->update($request->all());
        return redirect()->route('admin.packages')->with('success', 'Package updated successfully.');
    }


    public function showPackages(Request $request)
    {
        $locations = Location::all();
        $locationId = $request->input('location_id');

        // Query packages, applying the location filter if provided
        $packages = Package::when($locationId, function ($query, $locationId) {
            return $query->where('location_id', $locationId);
        })->paginate(10);

        return view('admin.packages', compact('packages', 'locations'));
    }

    public function deletePackage(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages')->with('success', 'Package deleted successfully.');
    }

    public function showVouchers(Request $request)
    {
        $locations = Location::all();
        $packages = Package::all();

        $vouchers = DB::table('vouchers')
            ->join('packages', 'vouchers.package_id', '=', 'packages.id')
            ->join('locations', 'packages.location_id', '=', 'locations.id')
            ->select(
                'vouchers.*',
                'packages.name as package_name',
                'packages.cost',
                'locations.name as location_name'
            )
            ->when($request->location_id, function ($query, $locationId) {
                return $query->where('packages.location_id', $locationId);
            })
            ->when($request->package_id, function ($query, $packageId) {
                return $query->where('vouchers.package_id', $packageId);
            })
            ->paginate(10);

        return view('admin.vouchers', compact('vouchers', 'locations', 'packages'));
    }

public function getPackagesByLocation($locationId)
{
    $packages = Package::where('location_id', $locationId)->get();
    return response()->json($packages);
}

public function bulkAction(Request $request)
{
    $voucherIds = $request->input('voucher_ids', []);
    $status = $request->input('status');

    if ($request->isMethod('post') && !empty($voucherIds)) {
        if ($status) {
            // Change status
            Voucher::whereIn('id', $voucherIds)->update(['is_used' => $status === 'used']);
        } else {
            // Delete vouchers
            Voucher::destroy($voucherIds);
        }
    }

    return redirect()->route('admin.vouchers')->with('success', 'Action completed successfully.');
}

}
