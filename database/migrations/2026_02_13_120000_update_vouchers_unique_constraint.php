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
        Schema::table('vouchers', function (Blueprint $table) {
            // Check if the unique index exists before dropping it (using standard naming convention)
            // Or just try to drop it. The default name is table_column_unique.
            $table->dropUnique(['code']);
            
            // Add composite unique index
            $table->unique(['site_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropUnique(['site_id', 'code']);
            $table->unique('code');
        });
    }
};
