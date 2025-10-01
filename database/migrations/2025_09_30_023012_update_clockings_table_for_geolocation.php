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
        Schema::table('clockings', function (Blueprint $table) {
            //
            $table->decimal('clock_in_lat', 10, 7)->nullable()->after("clock_in_time");

            // 2. Add missing clock-in longitude column
            $table->decimal('clock_in_lon', 10, 7)->nullable()->after('clock_in_lat');

            // 3. Add clock-out latitude and longitude columns
            $table->decimal('clock_out_lat', 10, 7)->nullable()->after('clock_out_time');
            $table->decimal('clock_out_lon', 10, 7)->nullable()->after('clock_out_lat');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clockings', function (Blueprint $table) {
            //
        });
    }
};
