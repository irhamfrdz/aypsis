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
        Schema::table('tagihan_ob', function (Blueprint $table) {
            // Tambahkan kolom kegiatan setelah voyage
            if (!Schema::hasColumn('tagihan_ob', 'kegiatan')) {
                $table->string('kegiatan', 50)->nullable()->after('voyage')->comment('Jenis kegiatan: muat atau bongkar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_ob', 'kegiatan')) {
                $table->dropColumn('kegiatan');
            }
        });
    }
};
