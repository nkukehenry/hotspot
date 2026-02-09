<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$version = DB::select('SELECT VERSION() as version')[0]->version;
echo "Database Version: $version\n";
