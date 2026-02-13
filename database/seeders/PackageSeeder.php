<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $downtown = \App\Models\Site::where('name', 'Downtown')->first();
        $uptown = \App\Models\Site::where('name', 'Uptown')->first();

        Package::create([
            'name' => 'Basic Package',
            'cost' => 1000,
            'description' => 'Basic internet package',
            'site_id' => $downtown->id,
        ]);

        Package::create([
            'name' => 'Premium Package',
            'cost' => 2000,
            'description' => 'Premium internet package',
            'site_id' => $uptown->id,
        ]);
    }
}
