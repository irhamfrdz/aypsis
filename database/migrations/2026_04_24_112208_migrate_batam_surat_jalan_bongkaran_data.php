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
        // 1. Prepare schema (add new columns)
        if (!Schema::hasColumn('uang_jalans', 'surat_jalan_bongkaran_batam_id')) {
            Schema::table('uang_jalans', function (Blueprint $table) {
                $table->unsignedBigInteger('surat_jalan_bongkaran_batam_id')->nullable()->after('surat_jalan_bongkaran_id');
            });
        }

        // 2. Drop old foreign keys that point to the old table for Batam records
        // Using a more robust check for older MySQL versions
        $existingFkTt = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tanda_terima_bongkaran_batams' AND CONSTRAINT_NAME = 'ttbb_sj_bongkaran_foreign'");
        if (!empty($existingFkTt)) {
            DB::statement('ALTER TABLE tanda_terima_bongkaran_batams DROP FOREIGN KEY ttbb_sj_bongkaran_foreign');
        }

        $existingFkUj = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'uang_jalans' AND CONSTRAINT_NAME = 'uang_jalans_surat_jalan_bongkaran_id_foreign'");
        if (!empty($existingFkUj)) {
            DB::statement('ALTER TABLE uang_jalans DROP FOREIGN KEY uang_jalans_surat_jalan_bongkaran_id_foreign');
        }

        // 3. Move Data
        $batamRecords = DB::table('surat_jalan_bongkarans')->where('lokasi', 'batam')->get();

        foreach ($batamRecords as $record) {
            // Remove 'id' if we want auto-increment, but we want to KEEP the ID to maintain links
            $data = (array) $record;
            
            // Ensure any columns that might not exist in the new table are handled
            if (isset($data['nomor_surat_jalan'])) {
                $data['no_surat_jalan'] = $data['nomor_surat_jalan'];
                unset($data['nomor_surat_jalan']);
            }
            
            // Map 'ya'/'tidak' to boolean for lanjut_muat
            if (isset($data['lanjut_muat'])) {
                $data['lanjut_muat'] = ($data['lanjut_muat'] === 'ya' || $data['lanjut_muat'] === 1) ? 1 : 0;
            }

            // Map other booleans if they are strings
            foreach (['lembur', 'nginap', 'tidak_lembur_nginap'] as $boolCol) {
                if (isset($data[$boolCol])) {
                    $data[$boolCol] = ($data[$boolCol] === 'ya' || $data[$boolCol] === 1 || $data[$boolCol] === true) ? 1 : 0;
                }
            }

            // Handle non-nullable columns that might be null in old data
            if (!isset($data['uang_jalan_nominal']) || $data['uang_jalan_nominal'] === null) {
                $data['uang_jalan_nominal'] = 0;
            }
            
            // Remove columns that don't exist in the new table
            unset($data['tanggal_checkpoint']);
            
            DB::table('surat_jalan_bongkaran_batams')->insert($data);

            // Update Uang Jalans to point to the new ID in the new column
            DB::table('uang_jalans')
                ->where('surat_jalan_bongkaran_id', $record->id)
                ->update([
                    'surat_jalan_bongkaran_batam_id' => $record->id,
                    'surat_jalan_bongkaran_id' => null
                ]);
            
            // Delete from old table
            DB::table('surat_jalan_bongkarans')->where('id', $record->id)->delete();
        }

        // 4. Re-establish Foreign Keys pointing to the NEW tables
        $checkTt = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tanda_terima_bongkaran_batams' AND CONSTRAINT_NAME = 'ttbb_sj_bongkaran_batam_foreign'");
        if (empty($checkTt)) {
            Schema::table('tanda_terima_bongkaran_batams', function (Blueprint $table) {
                $table->foreign('surat_jalan_bongkaran_id', 'ttbb_sj_bongkaran_batam_foreign')
                    ->references('id')->on('surat_jalan_bongkaran_batams')
                    ->onDelete('cascade');
            });
        }

        $checkUjBatam = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'uang_jalans' AND CONSTRAINT_NAME = 'uj_sjb_batam_foreign'");
        if (empty($checkUjBatam)) {
            Schema::table('uang_jalans', function (Blueprint $table) {
                $table->foreign('surat_jalan_bongkaran_batam_id', 'uj_sjb_batam_foreign')
                    ->references('id')->on('surat_jalan_bongkaran_batams')
                    ->onDelete('cascade');
            });
        }

        $checkUjOld = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'uang_jalans' AND CONSTRAINT_NAME = 'uang_jalans_surat_jalan_bongkaran_id_foreign'");
        if (empty($checkUjOld)) {
            Schema::table('uang_jalans', function (Blueprint $table) {
                // Re-add the old FK for Jakarta records
                $table->foreign('surat_jalan_bongkaran_id', 'uang_jalans_surat_jalan_bongkaran_id_foreign')
                    ->references('id')->on('surat_jalan_bongkarans')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Reverse is complex, but basically move back
        $batamRecords = DB::table('surat_jalan_bongkaran_batams')->get();

        Schema::table('tanda_terima_bongkaran_batams', function (Blueprint $table) {
            try {
                $table->dropForeign('ttbb_sj_bongkaran_batam_foreign');
            } catch (\Exception $e) {}
        });

        Schema::table('uang_jalans', function (Blueprint $table) {
            try {
                $table->dropForeign('uj_sjb_batam_foreign');
            } catch (\Exception $e) {}
        });

        foreach ($batamRecords as $record) {
            $data = (array) $record;
            DB::table('surat_jalan_bongkarans')->insert($data);

            DB::table('uang_jalans')
                ->where('surat_jalan_bongkaran_batam_id', $record->id)
                ->update([
                    'surat_jalan_bongkaran_id' => $record->id,
                    'surat_jalan_bongkaran_batam_id' => null
                ]);

            DB::table('surat_jalan_bongkaran_batams')->where('id', $record->id)->delete();
        }

        Schema::table('tanda_terima_bongkaran_batams', function (Blueprint $table) {
            $table->foreign('surat_jalan_bongkaran_id', 'ttbb_sj_bongkaran_foreign')
                ->references('id')->on('surat_jalan_bongkarans')
                ->onDelete('cascade');
        });

        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->dropColumn('surat_jalan_bongkaran_batam_id');
        });
    }
};
