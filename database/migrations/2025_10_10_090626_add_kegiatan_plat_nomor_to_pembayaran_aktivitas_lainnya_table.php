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
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'kegiatan')) {
                $table->string('kegiatan')->nullable()->after('aktivitas_pembayaran');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'plat_nomor')) {
                $table->string('plat_nomor')->nullable()->after('kegiatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'plat_nomor')) {
                $table->dropColumn('plat_nomor');
            }
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'kegiatan')) {
                $table->dropColumn('kegiatan');
            }
        });
    }
};
