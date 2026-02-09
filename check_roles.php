<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "--- User Roles Review ---\n";
User::all()->each(function($user) {
    echo "User: {$user->email} | Site ID: {$user->site_id} | Roles: " . $user->getRoleNames()->implode(', ') . "\n";
});

echo "\n--- Global Roles Check ---\n";
Role::all()->each(function($role) {
    echo "Role: {$role->name}\n";
});
