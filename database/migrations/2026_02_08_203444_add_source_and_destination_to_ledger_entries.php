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
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('source_account_id')->nullable()->after('account_id');
            $table->unsignedBigInteger('destination_account_id')->nullable()->after('source_account_id');

            $table->foreign('source_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('destination_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropForeign(['source_account_id']);
            $table->dropForeign(['destination_account_id']);
            $table->dropColumn(['source_account_id', 'destination_account_id']);
        });
    }
};
