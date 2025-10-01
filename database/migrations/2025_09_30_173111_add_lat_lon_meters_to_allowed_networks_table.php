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
        Schema::table('allowed_networks', function (Blueprint $table) {
            //
            $table->decimal('lat', 10, 7)->nullable()->after('ip_address'); // Latitude
            $table->decimal('lon', 10, 7)->nullable()->after('lat'); // Longitude
            $table->integer('meters_allowed')->default(100)->after('lon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowed_networks', function (Blueprint $table) {
            //
        });
    }
};
