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
        Schema::table('mobils', function (Blueprint $table) {
            // Hapus kolom lama yang tidak digunakan lagi
            $table->dropColumn(['aktiva', 'plat', 'ukuran']);
            
            // Tambah kolom baru sesuai kebutuhan
            $table->string('kode_no')->unique()->after('id');
            $table->string('nomor_polisi')->unique()->after('kode_no');
            $table->string('lokasi')->nullable()->after('nomor_polisi');
            $table->string('merek')->nullable()->after('lokasi');
            $table->string('jenis')->nullable()->after('merek');
            $table->year('tahun_pembuatan')->nullable()->after('jenis');
            $table->string('bpkb')->nullable()->after('tahun_pembuatan');
            $table->string('no_mesin')->nullable()->after('bpkb');
            // nomor_rangka sudah ada, tidak perlu ditambah lagi
            $table->date('pajak_stnk')->nullable()->after('nomor_rangka');
            $table->date('pajak_plat')->nullable()->after('pajak_stnk');
            $table->string('no_kir')->nullable()->after('pajak_plat');
            $table->date('pajak_kir')->nullable()->after('no_kir');
            $table->string('atas_nama')->nullable()->after('pajak_kir');
            $table->unsignedBigInteger('karyawan_id')->nullable()->after('atas_nama');
            
            // Tambah foreign key constraint untuk karyawan_id
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('set null');
            
            // Update nomor_rangka agar tidak unik dan bisa nullable
            $table->dropUnique(['nomor_rangka']);
            $table->string('nomor_rangka')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['karyawan_id']);
            
            // Hapus kolom baru
            $table->dropColumn([
                'kode_no', 'nomor_polisi', 'lokasi', 'merek', 'jenis', 
                'tahun_pembuatan', 'bpkb', 'no_mesin', 'pajak_stnk', 
                'pajak_plat', 'no_kir', 'pajak_kir', 'atas_nama', 'karyawan_id'
            ]);
            
            // Kembalikan kolom lama
            $table->string('aktiva')->unique()->after('id');
            $table->string('plat')->unique()->after('aktiva');
            $table->string('ukuran')->after('nomor_rangka');
            
            // Kembalikan nomor_rangka ke unique
            $table->string('nomor_rangka')->unique()->change();
        });
    }
};
