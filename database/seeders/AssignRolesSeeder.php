<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AssignRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Assign Owner Role to main admin
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->assignRole('Owner');
            $this->command->info('Assigned Owner role to ' . $admin->email);
        } else {
            // Create if not exists (fallback)
            $admin = User::create([
                'name' => 'Platform Owner',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'site_id' => null,
            ]);
            $admin->assignRole('Owner');
            $this->command->info('Created Owner user: admin@example.com');
        }

        // 2. Create Managers for existing sites
        $sites = Site::all();
        if ($sites->isEmpty()) {
            $this->command->warn('No sites found. Cannot create Site Managers.');
            return;
        }

        foreach ($sites as $site) {
            $email = 'manager@' . ($site->slug ?? strtolower(str_replace(' ', '', $site->name))) . '.com';
            
            $manager = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $site->name . ' Manager',
                    'password' => Hash::make('password'),
                    'site_id' => $site->id,
                ]
            );
            
            $manager->syncRoles(['Manager']); // Ensure they only have Manager role
            $this->command->info("Created Manager for {$site->name}: {$email}");

            // 3. Create Agents for existing sites
            $agentEmail = 'agent@' . ($site->slug ?? strtolower(str_replace(' ', '', $site->name))) . '.com';
            $agent = User::firstOrCreate(
                ['email' => $agentEmail],
                [
                    'name' => $site->name . ' Agent',
                    'password' => Hash::make('password'),
                    'site_id' => $site->id,
                ]
            );
            $agent->syncRoles(['Agent']);
            $this->command->info("Created Agent for {$site->name}: {$agentEmail}");
        }

        // 4. Optional: Assign default role to remaining users without roles?
        // skipping for now as per minimal request logic.
    }
}
