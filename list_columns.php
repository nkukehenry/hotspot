<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$columns = Schema::getColumnListing('sites');

echo "Columns in 'sites' table:\n";
foreach ($columns as $column) {
    echo "- $column\n";
}
