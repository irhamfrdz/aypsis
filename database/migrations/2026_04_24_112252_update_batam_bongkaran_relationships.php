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
        // 1. Update Tanda Terima Bongkaran Batam to point to the new table
        $existingFkTt = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tanda_terima_bongkaran_batams' AND CONSTRAINT_NAME = 'ttbb_sj_bongkaran_foreign'");
        if (!empty($existingFkTt)) {
            DB::statement('ALTER TABLE tanda_terima_bongkaran_batams DROP FOREIGN KEY ttbb_sj_bongkaran_foreign');
        }

        // Check if the new FK already exists
        $foreignKeysTt = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tanda_terima_bongkaran_batams' AND CONSTRAINT_NAME = 'ttbb_sj_bongkaran_batam_foreign'");
        if (empty($foreignKeysTt)) {
            Schema::table('tanda_terima_bongkaran_batams', function (Blueprint $table) {
                $table->foreign('surat_jalan_bongkaran_id', 'ttbb_sj_bongkaran_batam_foreign')
                    ->references('id')->on('surat_jalan_bongkaran_batams')
                    ->onDelete('cascade');
            });
        }

        // 2. Add column to Uang Jalans to link to the new table
        if (!Schema::hasColumn('uang_jalans', 'surat_jalan_bongkaran_batam_id')) {
            Schema::table('uang_jalans', function (Blueprint $table) {
                $table->unsignedBigInteger('surat_jalan_bongkaran_batam_id')->nullable()->after('surat_jalan_bongkaran_id');
            });
        }

        // Check if the new FK for Uang Jalans already exists
        $foreignKeysUj = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'uang_jalans' AND CONSTRAINT_NAME = 'uj_sjb_batam_foreign'");
        if (empty($foreignKeysUj)) {
            Schema::table('uang_jalans', function (Blueprint $table) {
                $table->foreign('surat_jalan_bongkaran_batam_id', 'uj_sjb_batam_foreign')
                    ->references('id')->on('surat_jalan_bongkaran_batams')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->dropForeign('uj_sjb_batam_foreign');
            $table->dropColumn('surat_jalan_bongkaran_batam_id');
        });

        Schema::table('tanda_terima_bongkaran_batams', function (Blueprint $table) {
            $table->dropForeign('ttbb_sj_bongkaran_batam_foreign');
        });

        Schema::table('tanda_terima_bongkaran_batams', function (Blueprint $table) {
            $table->foreign('surat_jalan_bongkaran_id', 'ttbb_sj_bongkaran_foreign')
                ->references('id')->on('surat_jalan_bongkarans')
                ->onDelete('cascade');
        });
    }
};
