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

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
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

    public function showReports()
    {
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
            ->groupBy('packages.id', 'locations.id')
            ->get();

        return view('admin.reports', compact('salesData'));
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
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $packageId = $request->package_id;

        Excel::import(new VouchersImport($packageId), $request->file('file'));

        return redirect()->route('admin.showUploadVouchers')->with('success', 'Vouchers uploaded successfully.');
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


    public function showPackages()
    {
        $packages = Package::paginate(10);
        $locations = Location::all();
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
}
