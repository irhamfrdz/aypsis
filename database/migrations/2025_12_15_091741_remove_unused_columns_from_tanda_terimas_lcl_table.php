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
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['master_tujuan_kirim_id']);
            
            // Drop columns yang tidak digunakan
            $table->dropColumn([
                'nomor_kontainer',           // Pindah ke pivot kontainer_tanda_terima_lcl
                'size_kontainer',            // Tidak digunakan di form
                'tipe_kontainer',            // Hardcoded 'lcl', tidak perlu di DB
                'nama_barang',               // Pindah ke tanda_terima_lcl_items
                'keterangan_barang',         // Tidak ada di form
                'master_tujuan_kirim_id',    // Duplikat dengan tujuan_pengiriman
                'kegiatan',                  // Tidak ada di form
            ]);
            
            // Update tujuan_pengiriman dari nullable string menjadi foreign key
            $table->unsignedBigInteger('tujuan_pengiriman')->nullable()->change();
            $table->foreign('tujuan_pengiriman')->references('id')->on('master_tujuan_kirim')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['tujuan_pengiriman']);
            
            // Change tujuan_pengiriman back to string
            $table->string('tujuan_pengiriman')->nullable()->change();
            
            // Add back the removed columns
            $table->string('nomor_kontainer')->nullable()->index();
            $table->string('size_kontainer')->nullable();
            $table->enum('tipe_kontainer', ['fcl', 'lcl', 'cargo'])->default('lcl');
            $table->string('nama_barang')->nullable();
            $table->text('keterangan_barang')->nullable();
            $table->foreignId('master_tujuan_kirim_id')->nullable()->constrained('master_tujuan_kirim')->onDelete('set null');
            $table->string('kegiatan')->nullable();
        });
    }
};
