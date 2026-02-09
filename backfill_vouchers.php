<?php

use App\Models\Voucher;
use App\Models\Package;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

Voucher::withoutGlobalScopes()->with('package')->chunk(100, function($vouchers) {
    foreach($vouchers as $v) {
        if ($v->package) {
            $v->site_id = $v->package->site_id;
            $v->save();
        }
    }
});

echo "Backfill completed.\n";
