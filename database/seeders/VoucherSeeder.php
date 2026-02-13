<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Voucher;
use App\Models\Package;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basicPackage = Package::where('name', 'Basic Package')->first();
        $premiumPackage = Package::where('name', 'Premium Package')->first();

        Voucher::create([
            'code' => 'VOUCHER1234',
            'package_id' => $basicPackage->id,
            'site_id' => $basicPackage->site_id,
            'is_used' => false,
        ]);

        Voucher::create([
            'code' => 'VOUCHER5678',
            'package_id' => $premiumPackage->id,
            'site_id' => $premiumPackage->site_id,
            'is_used' => false,
        ]);
    }
}
