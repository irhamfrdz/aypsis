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
        Schema::table('kontainers', function (Blueprint $table) {
            // Drop unused columns
            $table->dropColumn([
                'tanggal_beli',
                'tanggal_jual',
                'kondisi_kontainer',
                'tanggal_kondisi_terakhir',
                'kontainer_asal',
                'pemilik_kontainer'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            // Restore dropped columns
            $table->date('tanggal_beli')->nullable()->after('tipe_kontainer');
            $table->date('tanggal_jual')->nullable()->after('tanggal_beli');
            $table->string('kondisi_kontainer')->nullable()->after('keterangan');
            $table->date('tanggal_kondisi_terakhir')->nullable()->after('kondisi_kontainer');
            $table->string('kontainer_asal')->nullable()->after('tahun_pembuatan');
            $table->string('pemilik_kontainer')->nullable()->after('tanggal_selesai_sewa');
        });
    }
};
