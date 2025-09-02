<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Periksa apakah tabel ada sebelum mencoba mengubahnya
        if (Schema::hasTable('master_kegiatans')) {
            Schema::table('master_kegiatans', function (Blueprint $table) {
                // Menambahkan kolom 'kode_kegiatan' setelah kolom 'id'
                // Kolom ini unik dan tidak boleh null.
                $table->string('kode_kegiatan')->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Periksa apakah tabel ada sebelum mencoba mengubahnya
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasTable('master_kegiatans')) {
            Schema::table('master_kegiatans', function (Blueprint $table) {
                $table->dropColumn('kode_kegiatan');
            });
        }
    }
};
