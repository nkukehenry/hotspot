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
            $table->string('settlement_momo_number')->nullable();
            $table->string('settlement_account_name')->nullable();
        });

        Schema::create('settlement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Admin who approved
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('settlement_request_id')->nullable()->constrained('settlement_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['settlement_request_id']);
            $table->dropColumn('settlement_request_id');
        });

        Schema::dropIfExists('settlement_requests');

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['settlement_momo_number', 'settlement_account_name']);
        });
    }
};
