<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create permissions
        $manageUsers = Permission::firstOrCreate(['name' => 'manage users']);
        $manageVouchers = Permission::firstOrCreate(['name' => 'manage vouchers']);
        $viewReports = Permission::firstOrCreate(['name' => 'view reports']);

        // Assign permissions to roles
        $adminRole->givePermissionTo([$manageUsers, $manageVouchers, $viewReports]);
        $customerRole->givePermissionTo([]); // Customers might not have specific permissions
    }
}
