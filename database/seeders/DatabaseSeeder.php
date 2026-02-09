<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            LocationSeeder::class,
            PackageSeeder::class,
            VoucherSeeder::class,
            UserSeeder::class,
            AssignRolesSeeder::class,
            SystemSettingSeeder::class,
            FinancialSeeder::class,
        ]);
    }
}
