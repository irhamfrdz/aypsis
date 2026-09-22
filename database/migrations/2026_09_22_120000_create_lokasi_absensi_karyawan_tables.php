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
        if (Schema::hasTable('lokasi_absensis')) {
            Schema::table('lokasi_absensis', function (Blueprint $table) {
                if (!Schema::hasColumn('lokasi_absensis', 'tipe_penugasan')) {
                    $table->string('tipe_penugasan', 20)->default('semua')->after('is_active');
                }
            });
        }

        if (!Schema::hasTable('lokasi_absensi_karyawan')) {
            Schema::create('lokasi_absensi_karyawan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lokasi_absensi_id');
                $table->unsignedBigInteger('karyawan_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->index('lokasi_absensi_id');
                $table->index('karyawan_id');
                $table->index('user_id');

                $table->foreign('lokasi_absensi_id')
                    ->references('id')
                    ->on('lokasi_absensis')
                    ->onDelete('cascade');

                $table->foreign('karyawan_id')
                    ->references('id')
                    ->on('karyawans')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_absensi_karyawan');

        if (Schema::hasTable('lokasi_absensis')) {
            Schema::table('lokasi_absensis', function (Blueprint $table) {
                if (Schema::hasColumn('lokasi_absensis', 'tipe_penugasan')) {
                    $table->dropColumn('tipe_penugasan');
                }
            });
        }
    }
};
