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
        Schema::table('prospek', function (Blueprint $table) {
            // Add columns for tracking surat jalan relationship
            $table->string('no_surat_jalan')->nullable()->after('tipe')->comment('Nomor surat jalan yang terkait');
            $table->unsignedBigInteger('surat_jalan_id')->nullable()->after('no_surat_jalan')->comment('ID surat jalan yang terkait');
            $table->unsignedBigInteger('tanda_terima_id')->nullable()->after('surat_jalan_id')->comment('ID tanda terima yang terkait');
            
            // Add more detailed fields that might be missing
            $table->decimal('total_ton', 8, 3)->nullable()->after('tujuan_pengiriman')->comment('Total berat dalam ton');
            $table->integer('kuantitas')->nullable()->after('total_ton')->comment('Jumlah barang');
            $table->decimal('total_volume', 8, 3)->nullable()->after('kuantitas')->comment('Total volume');
            $table->unsignedBigInteger('kapal_id')->nullable()->after('nama_kapal')->comment('ID kapal yang terkait');
            $table->string('no_voyage')->nullable()->after('kapal_id')->comment('Nomor voyage');
            $table->string('pelabuhan_asal')->nullable()->after('no_voyage')->comment('Pelabuhan asal');
            $table->date('tanggal_muat')->nullable()->after('pelabuhan_asal')->comment('Tanggal muat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            // Drop the added columns in reverse order
            $table->dropColumn([
                'tanggal_muat',
                'pelabuhan_asal',
                'no_voyage',
                'kapal_id',
                'total_volume',
                'kuantitas',
                'total_ton',
                'tanda_terima_id',
                'surat_jalan_id',
                'no_surat_jalan'
            ]);
        });
    }
};
