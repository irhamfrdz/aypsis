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
        Schema::table('shipper_consignees', function (Blueprint $table) {
            $table->string('document_ppftz_03')->nullable();
            $table->string('condition')->nullable();
            $table->string('ip_bp_kawasan')->nullable();
            $table->string('npwp_consignee_16_digit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_consignees', function (Blueprint $table) {
            $table->dropColumn([
                'document_ppftz_03',
                'condition',
                'ip_bp_kawasan',
                'npwp_consignee_16_digit'
            ]);
        });
    }
};
