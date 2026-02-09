<?php

use Spatie\Permission\Models\Role;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$mapping = [
    'Site Manager' => 'Manager',
    'Site Supervisor' => 'Supervisor',
    'Site Agent' => 'Agent',
];

foreach ($mapping as $old => $new) {
    $role = Role::where('name', $old)->first();
    if ($role) {
        // Check if new role already exists
        $newRole = Role::where('name', $new)->first();
        if (!$newRole) {
            echo "Renaming role '{$old}' to '{$new}'...\n";
            $role->name = $new;
            $role->save();
        } else {
            echo "Merging users from '{$old}' to existing '{$new}'...\n";
            // Assign users of old role to new role
            $users = User::role($old)->get();
            foreach ($users as $user) {
                $user->assignRole($new);
                $user->removeRole($old);
            }
            $role->delete();
        }
    }
}

echo "Role standardization completed.\n";
