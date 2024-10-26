<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Location;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $downtown = Location::where('name', 'Downtown')->first();
        $uptown = Location::where('name', 'Uptown')->first();

        Package::create([
            'name' => 'Basic Package',
            'cost' => 1000,
            'description' => 'Basic internet package',
            'icon' => 'basic.png',
            'location_id' => $downtown->id,
        ]);

        Package::create([
            'name' => 'Premium Package',
            'cost' => 2000,
            'description' => 'Premium internet package',
            'icon' => 'premium.png',
            'location_id' => $uptown->id,
        ]);
    }
}
