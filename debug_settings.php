<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->handle(new Illuminate\Http\Request());
$settings = \App\Models\SystemSetting::first();
echo json_encode($settings);
