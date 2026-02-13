<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::paginate(10);
        return view('admin.sites', compact('sites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $data = $request->except('logo');
        
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
            \Illuminate\Support\Facades\Log::info("Site logo uploaded: " . $logoPath);
        }

        Site::create($data);

        return redirect()->route('admin.sites')->with('success', 'Site created successfully.');
    }

    public function update(Request $request, Site $site)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
            \Illuminate\Support\Facades\Log::info("Site logo updated for site {$site->id}: " . $logoPath);
        }

        $site->update($data);

        return redirect()->route('admin.sites')->with('success', 'Site updated successfully.');
    }

    public function show(Site $site)
    {
        $transactions = $site->transactions()->latest()->take(20)->get(); 
        $packages = $site->packages;
        $users = $site->users;
        
        // Fetch Site Payable Account
        $siteAccount = \App\Models\Account::where('site_id', $site->id)
            ->where('code', "SITE_PAYABLE_" . $site->id)
            ->first();

        $ledgerEntries = $siteAccount 
            ? $siteAccount->ledgerEntries()->latest()->take(20)->get() 
            : collect();

        $stakeholders = \App\Models\SiteStakeholder::where('site_id', $site->id)->with('account')->get();
        
        // Voucher Inventory Summary
        $voucherInventory = $site->packages->map(function ($package) {
            return [
                'package_name' => $package->name,
                'total' => $package->vouchers()->count(),
                'used' => $package->vouchers()->where('is_used', true)->count(),
                'available' => $package->vouchers()->where('is_used', false)->count(),
            ];
        });

        return view('admin.sites_show', compact(
            'site', 'transactions', 'packages', 'users', 
            'voucherInventory', 'siteAccount', 'ledgerEntries', 'stakeholders'
        ));
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return redirect()->route('admin.sites')->with('success', 'Site deleted successfully.');
    }
}
