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
            // Drop existing columns
            $table->dropColumn(['kode_tipe', 'nama_tipe', 'keterangan']);

            // Add correct columns
            $table->string('tipe_akun')->nullable()->after('id');
            $table->text('catatan')->nullable()->after('tipe_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe_akuns', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['tipe_akun', 'catatan']);

            // Restore old columns
            $table->string('kode_tipe')->nullable()->after('id');
            $table->string('nama_tipe')->nullable()->after('kode_tipe');
            $table->text('keterangan')->nullable()->after('nama_tipe');
        });
    }
};
