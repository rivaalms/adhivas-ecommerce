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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('address_line')->nullable();
            $table->string('subdistrict_id');
            $table->string('subdistrict_name');
            $table->string('district_id');
            $table->string('district_name');
            $table->string('regency_id');
            $table->string('regency_name');
            $table->string('province_id');
            $table->string('province_name');
            $table->string('postal_code');
            $table->boolean('is_default')->default(false);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
