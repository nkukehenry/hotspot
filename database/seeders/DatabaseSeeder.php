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
            RolePermissionSeeder::class,
            LocationSeeder::class,
            PackageSeeder::class,
            VoucherSeeder::class,
            UserSeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}
