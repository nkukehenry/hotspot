<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Site;
use App\Models\SiteStakeholder;

class FinancialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Core System Accounts
        $coreAccounts = [
            [
                'name' => 'System Digital Asset',
                'code' => 'SYSTEM_DIGITAL',
                'type' => 'asset',
            ],
            [
                'name' => 'System Cash Asset',
                'code' => 'SYSTEM_CASH',
                'type' => 'asset',
            ],
            [
                'name' => 'General Fee Income',
                'code' => 'FEE_INCOME',
                'type' => 'revenue',
            ],
        ];

        foreach ($coreAccounts as $acc) {
            Account::firstOrCreate(['code' => $acc['code']], $acc);
        }

        // 2. Create Platform Stakeholder Accounts (where fees go)
        $stakeholders = [
            'PLATFORM_OWNER' => 'Platform Owner Share',
            'DEVELOPER' => 'Developer Share',
            'PAYMENT_PROVIDER' => 'Payment Provider Share',
        ];

        foreach ($stakeholders as $code => $name) {
            Account::firstOrCreate(['code' => "STAKEHOLDER_PAYABLE_" . $code], [
                'name' => $name,
                'code' => "STAKEHOLDER_PAYABLE_" . $code,
                'type' => 'liability',
            ]);
        }

        // 3. Initialize Site Accounts for existing sites
        $sites = Site::all();
        foreach ($sites as $site) {
            // Ensure site has a payable account
            $siteAccountCode = "SITE_PAYABLE_" . $site->id;
            $siteAccount = Account::firstOrCreate(['code' => $siteAccountCode], [
                'name' => "Payable: " . $site->name,
                'code' => $siteAccountCode,
                'type' => 'liability',
                'site_id' => $site->id,
            ]);

            // Assign default stakeholders to site if none exist
            if (SiteStakeholder::where('site_id', $site->id)->count() === 0) {
                 // Sample Distribution: 40% Owner, 30% Developer, 30% Provider
                 SiteStakeholder::create([
                     'site_id' => $site->id,
                     'name' => 'Platform Owner',
                     'share_percent' => 40.00,
                     'account_id' => Account::where('code', 'STAKEHOLDER_PAYABLE_PLATFORM_OWNER')->first()->id
                 ]);
                 SiteStakeholder::create([
                     'site_id' => $site->id,
                     'name' => 'Developer',
                     'share_percent' => 30.00,
                     'account_id' => Account::where('code', 'STAKEHOLDER_PAYABLE_DEVELOPER')->first()->id
                 ]);
                 SiteStakeholder::create([
                     'site_id' => $site->id,
                     'name' => 'Payment Provider',
                     'share_percent' => 30.00,
                     'account_id' => Account::where('code', 'STAKEHOLDER_PAYABLE_PAYMENT_PROVIDER')->first()->id
                 ]);
            }

            // Set default fees if zero
            if ($site->customer_fee_fixed == 0 && $site->customer_fee_percent == 0) {
                $site->update([
                    'customer_fee_fixed' => 100.00, // 100 UGX flat
                    'customer_fee_percent' => 2.00, // 2% 
                    'site_fee_fixed' => 0,
                    'site_fee_percent' => 5.00, // 5% deducted from site
                ]);
            }
        }
    }
}
