<?php

use App\Models\User;
use App\Models\Voucher;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 1. Find a Site Manager
$manager = User::role('Manager')->whereNotNull('site_id')->first();

if (!$manager) {
    echo "No Site Manager found for testing.\n";
    exit;
}

echo "Testing as Manager: {$manager->name} (Site ID: {$manager->site_id})\n";

// Login as manager
Auth::login($manager);

try {
    echo "Querying Vouchers...\n";
    $vouchers = Voucher::take(5)->get();
    echo "Vouchers count: " . $vouchers->count() . "\n";
    foreach ($vouchers as $v) {
        if ($v->site_id != $manager->site_id) {
            echo "ERROR: Found voucher from another site: {$v->site_id}\n";
        }
    }

    echo "Querying Packages...\n";
    $packages = Package::all();
    echo "Packages count: " . $packages->count() . "\n";

    echo "Querying Transactions...\n";
    $transactions = Transaction::take(5)->get();
    echo "Transactions count: " . $transactions->count() . "\n";

    echo "SUCCESS: SiteScope applied without 500 error.\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
