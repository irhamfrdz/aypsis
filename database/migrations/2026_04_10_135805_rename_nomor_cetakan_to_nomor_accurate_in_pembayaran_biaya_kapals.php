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
        Schema::table('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->renameColumn('nomor_cetakan', 'nomor_accurate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->renameColumn('nomor_accurate', 'nomor_cetakan');
        });
    }
};
