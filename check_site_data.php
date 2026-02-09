<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$sites = DB::table('sites')->get(['id', 'name', 'location_code', 'code']);

echo "Sites data:\n";
foreach ($sites as $site) {
    echo "ID: {$site->id}, Name: {$site->name}, LocCode: {$site->location_code}, Code: {$site->code}\n";
}
