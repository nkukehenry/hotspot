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
        // 1. Rename locations to sites
        if (Schema::hasTable('locations') && !Schema::hasTable('sites')) {
            Schema::rename('locations', 'sites');
        }

        // 2. Add new columns to sites
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('sites', 'status')) {
                $table->string('status')->default('active')->after('slug');
            }
            if (!Schema::hasColumn('sites', 'digital_sales_balance')) {
                $table->decimal('digital_sales_balance', 15, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('sites', 'cash_sales_balance')) {
                $table->decimal('cash_sales_balance', 15, 2)->default(0)->after('digital_sales_balance');
            }
        });

        // 3. Update related tables
        // packages
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'location_id') && !Schema::hasColumn('packages', 'site_id')) {
                $table->renameColumn('location_id', 'site_id');
            }
        });

        // vouchers
        Schema::table('vouchers', function (Blueprint $table) {
             if (Schema::hasColumn('vouchers', 'location_id') && !Schema::hasColumn('vouchers', 'site_id')) {
                $table->renameColumn('location_id', 'site_id');
            }
        });

        // transactions - Transactions do NOT have location_id currently, so we add site_id
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'site_id')) {
                $table->unsignedBigInteger('site_id')->nullable()->after('id');
                // We could add a foreign key if we strictly know it won't fail with existing data
                // $table->foreign('site_id')->references('id')->on('sites');
            }
        });

        // 4. Add site_id to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'site_id')) {
                $table->unsignedBigInteger('site_id')->nullable()->after('id');
                // $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse 4. Users
        Schema::table('users', function (Blueprint $table) {
             if (Schema::hasColumn('users', 'site_id')) {
                $table->dropColumn('site_id');
            }
        });

        // Reverse 3. Related tables
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'site_id')) {
                 $table->dropColumn('site_id');
            }
        });

        Schema::table('vouchers', function (Blueprint $table) {
             if (Schema::hasColumn('vouchers', 'site_id') && !Schema::hasColumn('vouchers', 'location_id')) {
                $table->renameColumn('site_id', 'location_id');
            }
        });

        Schema::table('packages', function (Blueprint $table) {
             if (Schema::hasColumn('packages', 'site_id') && !Schema::hasColumn('packages', 'location_id')) {
                $table->renameColumn('site_id', 'location_id');
            }
        });

        // Reverse 2. Sites columns
        Schema::table('sites', function (Blueprint $table) {
             $table->dropColumn(['slug', 'status', 'digital_sales_balance', 'cash_sales_balance']);
        });

        // Reverse 1. Rename back
        if (Schema::hasTable('sites') && !Schema::hasTable('locations')) {
            Schema::rename('sites', 'locations');
        }
    }
};
