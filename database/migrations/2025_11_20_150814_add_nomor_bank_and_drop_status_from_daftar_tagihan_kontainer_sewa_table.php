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
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Tambah kolom nomor_bank setelah kolom adjustment_note
            $table->string('nomor_bank')->nullable()->after('adjustment_note');
            
            // Hapus kolom status jika ada
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Hapus kolom nomor_bank
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'nomor_bank')) {
                $table->dropColumn('nomor_bank');
            }
            
            // Kembalikan kolom status
            $table->string('status')->nullable()->after('adjustment_note');
        });
    }
};
