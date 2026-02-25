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
        Schema::table('master_pricelist_labuh_tambat', function (Blueprint $table) {
            $table->renameColumn('nama_tarif', 'nama_agen');
            $table->renameColumn('biaya', 'harga');
            $table->renameColumn('satuan', 'lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_labuh_tambat', function (Blueprint $table) {
            $table->renameColumn('nama_agen', 'nama_tarif');
            $table->renameColumn('harga', 'biaya');
            $table->renameColumn('lokasi', 'satuan');
        });
    }
};
