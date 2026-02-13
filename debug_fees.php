<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@example.com')->first(); // Assuming admin user or just run query without user scope if generic
// Actually, let's just run the query as is
$settlements = App\Models\SettlementRequest::with('site')
    ->withSum('transactions', 'site_fee')
    ->latest()
    ->get(); // Use get() instead of paginate for debug

foreach ($settlements as $s) {
    echo "ID: " . $s->id . " | Amount: " . $s->amount . " | Fee Sum: " . ($s->transactions_sum_site_fee ?? 'NULL') . "\n";
    
    // Double check specific transactions
    $txs = $s->transactions()->get(['id', 'amount', 'site_fee']);
    $manualSum = $txs->sum('site_fee');
    echo "  Manual Sum: " . $manualSum . "\n";
    foreach($txs as $tx) {
         echo "    Tx ID: " . $tx->id . " Fee: " . $tx->site_fee . "\n";
    }
}
