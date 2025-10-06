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
        Schema::table('kode_nomor', function (Blueprint $table) {
            // Check if columns exist before dropping them
            if (Schema::hasColumn('kode_nomor', 'nama')) {
                $table->dropColumn('nama');
            }
            if (Schema::hasColumn('kode_nomor', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
            // Only add catatan if it doesn't exist
            if (!Schema::hasColumn('kode_nomor', 'catatan')) {
                $table->text('catatan')->nullable()->after('kode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_nomor', function (Blueprint $table) {
            if (Schema::hasColumn('kode_nomor', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (!Schema::hasColumn('kode_nomor', 'nama')) {
                $table->string('nama')->after('kode');
            }
            if (!Schema::hasColumn('kode_nomor', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('nama');
            }
        });
    }
};
