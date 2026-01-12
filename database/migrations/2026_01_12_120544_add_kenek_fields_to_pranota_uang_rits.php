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
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            // Tambahkan kolom kenek jika belum ada
            if (!Schema::hasColumn('pranota_uang_rits', 'kenek_nama')) {
                $table->string('kenek_nama')->nullable()->after('supir_nama');
            }
            
            // Tambahkan kolom untuk membedakan uang rit supir dan kenek
            if (!Schema::hasColumn('pranota_uang_rits', 'uang_rit_supir')) {
                $table->decimal('uang_rit_supir', 15, 2)->default(0)->after('uang_rit');
            }
            if (!Schema::hasColumn('pranota_uang_rits', 'uang_rit_kenek')) {
                $table->decimal('uang_rit_kenek', 15, 2)->default(0)->after('uang_rit_supir');
            }
            
            // Tambahkan kolom total rit
            if (!Schema::hasColumn('pranota_uang_rits', 'total_rit')) {
                $table->decimal('total_rit', 15, 2)->default(0)->after('uang_rit_kenek');
            }
            
            // Tambahkan kolom BPJS
            if (!Schema::hasColumn('pranota_uang_rits', 'total_bpjs')) {
                $table->decimal('total_bpjs', 15, 2)->default(0)->after('total_tabungan');
            }
            
            // Tambahkan foreign key untuk surat jalan bongkaran
            if (!Schema::hasColumn('pranota_uang_rits', 'surat_jalan_bongkaran_id')) {
                $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable()->after('surat_jalan_id');
                $table->foreign('surat_jalan_bongkaran_id')->references('id')->on('surat_jalan_bongkarans')->onDelete('set null');
            }
            
            // Tambahkan index untuk kenek_nama jika belum ada
            if (!Schema::hasColumn('pranota_uang_rits', 'kenek_nama')) {
                $table->index('kenek_nama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            // Drop foreign key dan index terlebih dahulu
            $table->dropForeign(['surat_jalan_bongkaran_id']);
            $table->dropIndex(['kenek_nama']);
            
            // Drop kolom
            $table->dropColumn([
                'kenek_nama',
                'uang_rit_supir',
                'uang_rit_kenek',
                'total_rit',
                'total_bpjs',
                'surat_jalan_bongkaran_id'
            ]);
        });
    }
};
