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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->unsignedBigInteger('site_id')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });

        Schema::create('site_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->string('name');
            $table->decimal('share_percent', 5, 2);
            $table->unsignedBigInteger('account_id')->nullable(); // Account where funds are accumulated
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('transaction_id')->nullable(); // Link to sales transaction
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable(); // 'sale', 'payout', 'adjustment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('site_stakeholders');
        Schema::dropIfExists('accounts');
    }
};
