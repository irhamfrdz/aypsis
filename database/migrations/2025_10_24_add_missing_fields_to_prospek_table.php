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
        Schema::table('prospek', function (Blueprint $table) {
            // Cek dan tambah field tipe jika belum ada
            if (!Schema::hasColumn('prospek', 'tipe')) {
                $table->string('tipe')->nullable()->after('ukuran')->comment('Tipe kontainer: FCL, LCL, dll');
            }
            
            // Tambah field untuk tracking surat jalan agar pencarian lebih akurat
            if (!Schema::hasColumn('prospek', 'no_surat_jalan')) {
                $table->string('no_surat_jalan')->nullable()->after('nomor_kontainer')->comment('Nomor surat jalan untuk tracking');
            }
            
            if (!Schema::hasColumn('prospek', 'surat_jalan_id')) {
                $table->unsignedBigInteger('surat_jalan_id')->nullable()->after('no_surat_jalan')->comment('ID surat jalan untuk tracking');
            }
        });
        
        // Tambah index setelah kolom dibuat
        Schema::table('prospek', function (Blueprint $table) {
            // Cek dan buat index jika belum ada
            if (Schema::hasColumn('prospek', 'tipe')) {
                try {
                    $table->index('tipe');
                } catch (\Exception $e) {
                    // Index mungkin sudah ada, skip
                }
            }
            
            if (Schema::hasColumn('prospek', 'no_surat_jalan')) {
                try {
                    $table->index('no_surat_jalan');
                } catch (\Exception $e) {
                    // Index mungkin sudah ada, skip
                }
            }
            
            if (Schema::hasColumn('prospek', 'surat_jalan_id')) {
                try {
                    $table->index('surat_jalan_id');
                } catch (\Exception $e) {
                    // Index mungkin sudah ada, skip
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            // Drop index dulu
            $indexes = ['prospek_tipe_index', 'prospek_no_surat_jalan_index', 'prospek_surat_jalan_id_index'];
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Exception $e) {
                    // Index mungkin tidak ada, skip
                }
            }
            
            // Drop kolom
            if (Schema::hasColumn('prospek', 'surat_jalan_id')) {
                $table->dropColumn('surat_jalan_id');
            }
            if (Schema::hasColumn('prospek', 'no_surat_jalan')) {
                $table->dropColumn('no_surat_jalan');
            }
            if (Schema::hasColumn('prospek', 'tipe')) {
                $table->dropColumn('tipe');
            }
        });
    }
};