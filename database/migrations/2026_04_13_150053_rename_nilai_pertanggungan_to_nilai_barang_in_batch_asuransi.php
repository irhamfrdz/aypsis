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
        Schema::table('asuransi_tanda_terima_batches', function (Blueprint $table) {
            $table->renameColumn('total_nilai_pertanggungan', 'total_nilai_barang');
        });

        Schema::table('asuransi_tanda_terima_batch_items', function (Blueprint $table) {
            $table->renameColumn('nilai_pertanggungan', 'nilai_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asuransi_tanda_terima_batch_items', function (Blueprint $table) {
            $table->renameColumn('nilai_barang', 'nilai_pertanggungan');
        });

        Schema::table('asuransi_tanda_terima_batches', function (Blueprint $table) {
            $table->renameColumn('total_nilai_barang', 'total_nilai_pertanggungan');
        });
    }
};
