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
        Schema::table('master_chasis_batams', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['plat_nomor', 'merek', 'tahun_pembuatan', 'status']);

            // Add new columns
            $table->enum('kondisi', ['baik', 'rusak'])->default('baik')->after('tipe')->comment('Kondisi Chasis');
            $table->enum('lokasi', ['sm', 'relasi'])->default('sm')->after('kondisi')->comment('Lokasi Chasis (sm/relasi)');
            $table->date('tanggal_terakhir_pakai')->nullable()->after('lokasi')->comment('Tanggal Terakhir Pakai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_chasis_batams', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['kondisi', 'lokasi', 'tanggal_terakhir_pakai']);

            // Re-add old columns
            $table->string('plat_nomor', 50)->nullable()->comment('Nomor Polisi / Plat Chasis')->after('kode');
            $table->string('merek', 100)->nullable()->comment('Merek Chasis')->after('tipe');
            $table->integer('tahun_pembuatan')->nullable()->comment('Tahun Pembuatan')->after('merek');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->comment('Status Chasis')->after('catatan');
        });
    }
};
