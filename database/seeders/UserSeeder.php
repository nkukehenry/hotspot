<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Platform Owner
        $ownerEmail = 'admin@neonent.com';
        $owner = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => 'Platform Owner',
                'password' => Hash::make('password'),
                'site_id' => null, // Platform owner belongs to no specific site (or all)
            ]
        );
        $owner->assignRole('Owner');

        // Example Site Manager (optional, mainly for testing)
        // You might want to get a site ID first
        $site = \App\Models\Site::first();
        if ($site) {
             $manager = User::firstOrCreate(
                ['email' => 'manager@' . $site->slug . '.com'],
                [
                    'name' => 'Site Manager',
                    'password' => Hash::make('password'),
                    'site_id' => $site->id,
                ]
            );
            $manager->assignRole('Manager');
        }
    }
}
