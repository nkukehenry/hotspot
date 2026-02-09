<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL for renaming 'code' to 'site_code' because MariaDB < 10.5 doesn't support RENAME COLUMN
        // and Laravel's renameColumn might fail if dbal is missing or version detection is off.
        if (Schema::hasColumn('sites', 'code') && !Schema::hasColumn('sites', 'site_code')) {
            // Get the column definition. Assuming varchar(255) not null default 'dummy' based on previous migrations.
            DB::statement("ALTER TABLE sites CHANGE code site_code VARCHAR(255) NOT NULL DEFAULT 'dummy'");
        }

        Schema::table('sites', function (Blueprint $table) {
            // Drop 'location_code' if it exists (assuming 'site_code' is the one we want)
            if (Schema::hasColumn('sites', 'location_code')) {
                $table->dropColumn('location_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert site_code to code
        if (Schema::hasColumn('sites', 'site_code') && !Schema::hasColumn('sites', 'code')) {
             DB::statement("ALTER TABLE sites CHANGE site_code code VARCHAR(255) NOT NULL DEFAULT 'dummy'");
        }
        
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'location_code')) {
                $table->string('location_code')->unique()->nullable();
            }
        });
    }
};
