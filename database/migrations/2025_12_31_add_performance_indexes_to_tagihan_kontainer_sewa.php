<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Add indexes for commonly filtered/searched columns (only if they don't exist)
            $indexes = [
                'idx_vendor' => 'vendor',
                'idx_nomor_kontainer' => 'nomor_kontainer',
                'idx_size' => 'size',
                'idx_periode' => 'periode',
                'idx_group' => 'group',
                'idx_status_pranota' => 'status_pranota',
                'idx_tanggal_awal' => 'tanggal_awal',
                'idx_tanggal_akhir' => 'tanggal_akhir',
                'idx_vendor_kontainer' => ['vendor', 'nomor_kontainer'],
                'idx_kontainer_periode' => ['nomor_kontainer', 'periode'],
                'idx_vendor_periode' => ['vendor', 'periode'],
                'idx_group_periode' => ['group', 'periode'],
            ];

            $isSqlite = DB::getDriverName() === 'sqlite';
            foreach ($indexes as $indexName => $columns) {
                if ($isSqlite) {
                    $exists = DB::select("
                        SELECT name FROM sqlite_master WHERE type='index' AND name=?
                    ", [$indexName]);
                    if (empty($exists)) {
                        $table->index($columns, $indexName);
                    }

                    continue;
                }
                // Check if index already exists
                $exists = DB::select("
                    SELECT COUNT(*) as count
                    FROM information_schema.statistics
                    WHERE table_schema = DATABASE()
                    AND table_name = 'daftar_tagihan_kontainer_sewa'
                    AND index_name = ?
                ", [$indexName]);

                if ($exists[0]->count == 0) {
                    $table->index($columns, $indexName);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Drop indexes (only if they exist)
            $indexNames = [
                'idx_vendor',
                'idx_nomor_kontainer',
                'idx_size',
                'idx_periode',
                'idx_group',
                'idx_status_pranota',
                'idx_tanggal_awal',
                'idx_tanggal_akhir',
                'idx_vendor_kontainer',
                'idx_kontainer_periode',
                'idx_vendor_periode',
                'idx_group_periode',
            ];

            $isSqlite = DB::getDriverName() === 'sqlite';
            foreach ($indexNames as $indexName) {
                if ($isSqlite) {
                    $exists = DB::select("
                        SELECT name FROM sqlite_master WHERE type='index' AND name=?
                    ", [$indexName]);
                    if (! empty($exists)) {
                        $table->dropIndex($indexName);
                    }

                    continue;
                }
                // Check if index exists before dropping
                $exists = DB::select("
                    SELECT COUNT(*) as count
                    FROM information_schema.statistics
                    WHERE table_schema = DATABASE()
                    AND table_name = 'daftar_tagihan_kontainer_sewa'
                    AND index_name = ?
                ", [$indexName]);

                if ($exists[0]->count > 0) {
                    $table->dropIndex($indexName);
                }
            }
        });
    }
};
