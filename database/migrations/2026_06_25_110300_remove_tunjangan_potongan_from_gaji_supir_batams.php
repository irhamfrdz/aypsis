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
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            $table->dropColumn([
                'tunjangan_kehadiran',
                'tunjangan_makan',
                'tunjangan_lainnya',
                'potongan_bpjs',
                'potongan_pinjaman',
                'potongan_lainnya'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            $table->decimal('tunjangan_kehadiran', 15, 2)->default(0)->after('gaji_pokok');
            $table->decimal('tunjangan_makan', 15, 2)->default(0)->after('tunjangan_kehadiran');
            $table->decimal('tunjangan_lainnya', 15, 2)->default(0)->after('tunjangan_makan');
            $table->decimal('potongan_bpjs', 15, 2)->default(0)->after('tunjangan_lainnya');
            $table->decimal('potongan_pinjaman', 15, 2)->default(0)->after('potongan_bpjs');
            $table->decimal('potongan_lainnya', 15, 2)->default(0)->after('potongan_pinjaman');
        });
    }
};
