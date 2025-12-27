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
        Schema::table('surat_jalans', function (Blueprint $table) {
            // Checkpoint Kontainer Keluar fields
            if (!Schema::hasColumn('surat_jalans', 'status_checkpoint_keluar')) {
                $table->string('status_checkpoint_keluar')->nullable()->after('status')->comment('Status checkpoint keluar: null/pending/sudah_keluar');
            }
            if (!Schema::hasColumn('surat_jalans', 'waktu_keluar')) {
                $table->timestamp('waktu_keluar')->nullable()->after('status_checkpoint_keluar')->comment('Waktu kontainer keluar dari lokasi');
            }
            if (!Schema::hasColumn('surat_jalans', 'catatan_keluar')) {
                $table->text('catatan_keluar')->nullable()->after('waktu_keluar')->comment('Catatan saat kontainer keluar');
            }
            if (!Schema::hasColumn('surat_jalans', 'user_keluar_id')) {
                $table->unsignedBigInteger('user_keluar_id')->nullable()->after('catatan_keluar')->comment('User yang memproses checkpoint keluar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn(['status_checkpoint_keluar', 'waktu_keluar', 'catatan_keluar', 'user_keluar_id']);
        });
    }
};