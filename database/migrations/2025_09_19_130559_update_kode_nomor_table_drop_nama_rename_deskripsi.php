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
            // Rename deskripsi column to catatan
            $table->renameColumn('deskripsi', 'catatan');

            // Drop nama column
            $table->dropColumn('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_nomor', function (Blueprint $table) {
            // Add back nama column
            $table->string('nama')->after('saldo');

            // Rename catatan back to deskripsi
            $table->renameColumn('catatan', 'deskripsi');
        });
    }
};
