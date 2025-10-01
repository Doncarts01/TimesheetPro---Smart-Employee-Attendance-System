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
        Schema::create('allowed_networks', function (Blueprint $table) {
            $table->id();
            $table->string('network_name');
            $table->string('ip_address')->unique();
            $table->string('bssid')->nullable()->unique();
            $table->string('is_active')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowed_networks');
    }
};
