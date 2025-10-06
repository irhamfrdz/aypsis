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
        Schema::table('tipe_akuns', function (Blueprint $table) {
            // Check and drop existing columns if they exist
            if (Schema::hasColumn('tipe_akuns', 'kode_tipe')) {
                $table->dropColumn('kode_tipe');
            }
            if (Schema::hasColumn('tipe_akuns', 'nama_tipe')) {
                $table->dropColumn('nama_tipe');
            }
            if (Schema::hasColumn('tipe_akuns', 'keterangan')) {
                $table->dropColumn('keterangan');
            }

            // Add correct columns if they don't exist
            if (!Schema::hasColumn('tipe_akuns', 'tipe_akun')) {
                $table->string('tipe_akun')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tipe_akuns', 'catatan')) {
                $table->text('catatan')->nullable()->after('tipe_akun');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe_akuns', function (Blueprint $table) {
            // Check and drop new columns if they exist
            if (Schema::hasColumn('tipe_akuns', 'tipe_akun')) {
                $table->dropColumn('tipe_akun');
            }
            if (Schema::hasColumn('tipe_akuns', 'catatan')) {
                $table->dropColumn('catatan');
            }

            // Restore old columns if they don't exist
            if (!Schema::hasColumn('tipe_akuns', 'kode_tipe')) {
                $table->string('kode_tipe')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tipe_akuns', 'nama_tipe')) {
                $table->string('nama_tipe')->nullable()->after('kode_tipe');
            }
            if (!Schema::hasColumn('tipe_akuns', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('nama_tipe');
            }
        });
    }
};
