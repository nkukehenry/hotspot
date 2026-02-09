<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

function testRedirect($email) {
    $user = User::where('email', $email)->first();
    if (!$user) {
        echo "User {$email} not found.\n";
        return;
    }

    echo "Testing Redirect for: {$user->email} (Roles: " . $user->getRoleNames()->implode(', ') . ")\n";
    
    if ($user->can('view_owner_dashboard') || $user->can('view_manager_dashboard')) {
        echo " -> Redirect to: ADMIN DASHBOARD\n";
    } elseif ($user->can('view_agent_dashboard')) {
        echo " -> Redirect to: AGENT DASHBOARD\n";
    } else {
        echo " -> Redirect to: DEFAULT DASHBOARD\n";
    }
}

testRedirect('admin@example.com');
testRedirect('manager@uptown.com');
testRedirect('agent@downtown.com');
