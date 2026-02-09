<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\Transaction::whereNull('site_id')->chunk(100, function ($transactions) {
            foreach ($transactions as $transaction) {
                if ($transaction->voucher) {
                    $transaction->update([
                        'site_id' => $transaction->voucher->site_id,
                        'package_id' => $transaction->voucher->package_id,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No practical way to reverse a data backfill without knowing which were null
    }
};
