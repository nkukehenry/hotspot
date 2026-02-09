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
        Schema::table('sites', function (Blueprint $table) {
            $table->decimal('customer_fee_fixed', 15, 2)->default(0)->after('cash_sales_balance');
            $table->decimal('customer_fee_percent', 5, 2)->default(0)->after('customer_fee_fixed');
            $table->decimal('site_fee_fixed', 15, 2)->default(0)->after('customer_fee_percent');
            $table->decimal('site_fee_percent', 5, 2)->default(0)->after('site_fee_fixed');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('customer_fee', 15, 2)->default(0)->after('amount');
            $table->decimal('site_fee', 15, 2)->default(0)->after('customer_fee');
            $table->decimal('total_fee', 15, 2)->default(0)->after('site_fee');
            $table->decimal('total_amount', 15, 2)->default(0)->after('total_fee');
            $table->boolean('fee_distributed')->default(false)->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['customer_fee_fixed', 'customer_fee_percent', 'site_fee_fixed', 'site_fee_percent']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_fee', 'site_fee', 'total_fee', 'total_amount', 'fee_distributed']);
        });
    }
};
