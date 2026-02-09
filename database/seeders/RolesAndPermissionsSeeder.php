<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Granular Permissions
        $permissions = [
            // Sites
            'view_sites', 'create_sites', 'edit_sites', 'delete_sites',
            // Users
            'view_users', 'create_users', 'edit_users', 'delete_users',
            // Packages
            'view_packages', 'create_packages', 'edit_packages', 'delete_packages',
            // Vouchers
            'view_vouchers', 'create_vouchers', 'edit_vouchers', 'delete_vouchers',
            // roles
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            // Reports & Sales
            'view_reports', 'sell_vouchers', 'view_own_sales', 'view_transactions',
            // Dashboards
            'view_owner_dashboard', 'view_manager_dashboard', 'view_agent_dashboard', 'view_site_dashboard',
            // Settings
            'manage_settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Assign Permissions to Roles

        // Agent
        $role = Role::firstOrCreate(['name' => 'Agent']);
        $role->syncPermissions(['sell_vouchers', 'view_own_sales', 'view_transactions', 'view_agent_dashboard']);

        // Supervisor
        $role = Role::firstOrCreate(['name' => 'Supervisor']);
        $role->syncPermissions([
            'view_users', 'create_users', // Agents only (logic in controller)
            'view_vouchers', 'create_vouchers', // Uploads
            'view_packages',
            'view_transactions',
            'view_reports',
            'view_manager_dashboard', 'view_site_dashboard'
        ]);

        // Manager (Site Admin)
        $role = Role::firstOrCreate(['name' => 'Manager']);
        $role->syncPermissions([
            'view_sites', 
            // Users (Scoped)
            'view_users', 'create_users', 'edit_users', 'delete_users',
            // Packages (Scoped)
            'view_packages', 'create_packages', 'edit_packages', 'delete_packages',
            // Vouchers (Scoped)
            'view_vouchers', 'create_vouchers', 'edit_vouchers', 'delete_vouchers',
            // Roles (View only)
            'view_roles',
            'view_transactions',
            'view_reports',
            'view_manager_dashboard', 'view_site_dashboard'
        ]);

        // Owner (Platform Admin)
        $role = Role::firstOrCreate(['name' => 'Owner']);
        $role->syncPermissions(Permission::all()); // All permissions including create_sites, delete_sites, manage_roles, manage_settings
    }
}
