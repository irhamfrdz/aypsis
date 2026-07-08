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
        // Add columns if they don't exist
        Schema::table('checkpoints', function (Blueprint $table) {
            if (! Schema::hasColumn('checkpoints', 'bukti_timbangan')) {
                $table->text('bukti_timbangan')->nullable();
            }
            if (! Schema::hasColumn('checkpoints', 'bukti_timbangan_muat')) {
                $table->text('bukti_timbangan_muat')->nullable();
            }
        });

        Schema::table('surat_jalans', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_jalans', 'bukti_timbangan')) {
                $table->text('bukti_timbangan')->nullable();
            }
            if (! Schema::hasColumn('surat_jalans', 'bukti_timbangan_muat')) {
                $table->text('bukti_timbangan_muat')->nullable();
            }
        });

        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_jalan_bongkarans', 'bukti_timbangan')) {
                $table->text('bukti_timbangan')->nullable();
            }
            if (! Schema::hasColumn('surat_jalan_bongkarans', 'bukti_timbangan_muat')) {
                $table->text('bukti_timbangan_muat')->nullable();
            }
        });

        Schema::table('tanda_terimas', function (Blueprint $table) {
            if (! Schema::hasColumn('tanda_terimas', 'bukti_timbangan')) {
                $table->text('bukti_timbangan')->nullable();
            }
            if (! Schema::hasColumn('tanda_terimas', 'bukti_timbangan_muat')) {
                $table->text('bukti_timbangan_muat')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            if (Schema::hasColumn('checkpoints', 'bukti_timbangan')) {
                $table->dropColumn('bukti_timbangan');
            }
            if (Schema::hasColumn('checkpoints', 'bukti_timbangan_muat')) {
                $table->dropColumn('bukti_timbangan_muat');
            }
        });

        Schema::table('surat_jalans', function (Blueprint $table) {
            if (Schema::hasColumn('surat_jalans', 'bukti_timbangan')) {
                $table->dropColumn('bukti_timbangan');
            }
            if (Schema::hasColumn('surat_jalans', 'bukti_timbangan_muat')) {
                $table->dropColumn('bukti_timbangan_muat');
            }
        });

        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            if (Schema::hasColumn('surat_jalan_bongkarans', 'bukti_timbangan')) {
                $table->dropColumn('bukti_timbangan');
            }
            if (Schema::hasColumn('surat_jalan_bongkarans', 'bukti_timbangan_muat')) {
                $table->dropColumn('bukti_timbangan_muat');
            }
        });

        Schema::table('tanda_terimas', function (Blueprint $table) {
            if (Schema::hasColumn('tanda_terimas', 'bukti_timbangan')) {
                $table->dropColumn('bukti_timbangan');
            }
            if (Schema::hasColumn('tanda_terimas', 'bukti_timbangan_muat')) {
                $table->dropColumn('bukti_timbangan_muat');
            }
        });
    }
};
