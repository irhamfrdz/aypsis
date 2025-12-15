<?php
/**
 * Script untuk fix migration error di server
 * Jalankan dengan: php fix_migration.php
 * Atau akses via browser: http://your-domain.com/fix_migration.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "==============================================\n";
echo "FIX MIGRATION ERROR - Tanda Terima LCL\n";
echo "==============================================\n\n";

try {
    // 1. Cek batch terakhir
    echo "1. Mengecek batch migration terakhir...\n";
    $lastBatch = DB::table('migrations')->max('batch');
    $nextBatch = $lastBatch + 1;
    echo "   Last batch: {$lastBatch}\n";
    echo "   Next batch: {$nextBatch}\n\n";

    // 2. List migration yang perlu ditambahkan
    $migrations = [
        '2025_10_25_124630_create_tanda_terima_lcl_table',
        '2025_10_25_124657_create_tanda_terima_lcl_items_table',
        '2025_10_27_103613_fix_tanda_terima_lcl_tujuan_pengiriman_foreign_key',
        '2025_10_27_130917_add_nomor_kontainer_to_tanda_terima_lcl_table',
        '2025_10_27_131639_add_size_kontainer_to_tanda_terima_lcl_table',
        '2025_10_27_131957_add_seal_columns_to_tanda_terima_lcl_table',
        '2025_10_27_150123_fix_size_kontainer_enum_values_in_tanda_terima_lcl_table',
        '2025_12_01_153953_drop_jenis_barang_id_from_tanda_terima_lcl_table',
        '2025_12_01_155316_add_item_details_to_tanda_terima_lcl_items_table',
        '2025_12_04_143703_make_nomor_tanda_terima_nullable_in_tanda_terima_lcl_table',
        '2025_12_04_160829_add_gambar_surat_jalan_to_tanda_terima_lcl_table',
        '2025_12_04_161933_add_jenis_kontainer_to_tanda_terima_lcl_table',
        '2025_12_15_090852_create_tanda_terima_lcl_tables',
        '2025_12_15_091741_remove_unused_columns_from_tanda_terimas_lcl_table',
        '2025_12_15_092519_add_item_number_and_fix_columns_to_tanda_terima_lcl_items_table',
        '2025_12_15_144001_drop_unused_pivot_tables_tanda_terima_lcl',
        '2025_12_15_144324_add_container_fields_to_tanda_terimas_lcl_table',
        '2025_12_15_144932_create_tanda_terima_lcl_kontainer_pivot_table',
        '2025_12_15_160000_add_single_penerima_pengirim_to_tanda_terimas_lcl_table',
    ];

    echo "2. Menambahkan migration records...\n";
    $added = 0;
    foreach ($migrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        
        if (!$exists) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $nextBatch
            ]);
            echo "   ✓ {$migration}\n";
            $added++;
        } else {
            echo "   - {$migration} (sudah ada)\n";
        }
    }
    echo "   Total ditambahkan: {$added}\n\n";

    // 3. Verifikasi tabel tanda_terimas_lcl ada
    echo "3. Verifikasi tabel tanda_terimas_lcl...\n";
    if (Schema::hasTable('tanda_terimas_lcl')) {
        echo "   ✓ Tabel tanda_terimas_lcl ada\n";
        
        // Cek kolom-kolom penting
        $columns = ['nomor_kontainer', 'size_kontainer', 'tipe_kontainer', 'nomor_seal', 'tanggal_seal', 'nama_penerima', 'nama_pengirim'];
        foreach ($columns as $col) {
            if (Schema::hasColumn('tanda_terimas_lcl', $col)) {
                echo "   ✓ Kolom {$col} ada\n";
            } else {
                echo "   ✗ Kolom {$col} TIDAK ADA (perlu ditambahkan manual)\n";
            }
        }
    } else {
        echo "   ✗ Tabel tanda_terimas_lcl TIDAK ADA!\n";
    }
    echo "\n";

    // 4. Tambahkan kolom yang missing (penerima & pengirim)
    echo "4. Menambahkan kolom penerima & pengirim jika belum ada...\n";
    $columnsToAdd = [
        'nama_penerima' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN nama_penerima VARCHAR(255) NULL AFTER term_id",
        'pic_penerima' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN pic_penerima VARCHAR(255) NULL AFTER nama_penerima",
        'telepon_penerima' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN telepon_penerima VARCHAR(255) NULL AFTER pic_penerima",
        'alamat_penerima' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN alamat_penerima TEXT NULL AFTER telepon_penerima",
        'nama_pengirim' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN nama_pengirim VARCHAR(255) NULL AFTER alamat_penerima",
        'pic_pengirim' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN pic_pengirim VARCHAR(255) NULL AFTER nama_pengirim",
        'telepon_pengirim' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN telepon_pengirim VARCHAR(255) NULL AFTER pic_pengirim",
        'alamat_pengirim' => "ALTER TABLE tanda_terimas_lcl ADD COLUMN alamat_pengirim TEXT NULL AFTER telepon_pengirim",
    ];
    
    foreach ($columnsToAdd as $column => $sql) {
        if (!Schema::hasColumn('tanda_terimas_lcl', $column)) {
            try {
                DB::statement($sql);
                echo "   ✓ Kolom {$column} ditambahkan\n";
            } catch (\Exception $e) {
                echo "   ✗ Gagal tambah {$column}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   - Kolom {$column} sudah ada\n";
        }
    }
    echo "\n";

    // 4b. Fix kolom tujuan_pengiriman_id
    echo "4b. Memperbaiki kolom tujuan_pengiriman...\n";
    if (Schema::hasColumn('tanda_terimas_lcl', 'tujuan_pengiriman') && 
        !Schema::hasColumn('tanda_terimas_lcl', 'tujuan_pengiriman_id')) {
        try {
            DB::statement("ALTER TABLE tanda_terimas_lcl CHANGE COLUMN tujuan_pengiriman tujuan_pengiriman_id BIGINT UNSIGNED NULL");
            echo "   ✓ Kolom tujuan_pengiriman di-rename ke tujuan_pengiriman_id\n";
        } catch (\Exception $e) {
            echo "   ✗ Gagal rename tujuan_pengiriman: " . $e->getMessage() . "\n";
        }
    } elseif (!Schema::hasColumn('tanda_terimas_lcl', 'tujuan_pengiriman_id')) {
        try {
            DB::statement("ALTER TABLE tanda_terimas_lcl ADD COLUMN tujuan_pengiriman_id BIGINT UNSIGNED NULL");
            echo "   ✓ Kolom tujuan_pengiriman_id ditambahkan\n";
        } catch (\Exception $e) {
            echo "   ✗ Gagal tambah tujuan_pengiriman_id: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   - Kolom tujuan_pengiriman_id sudah ada\n";
    }
    echo "\n";

    // 5. Verifikasi/Buat tabel tanda_terima_lcl_items
    echo "5. Verifikasi tabel tanda_terima_lcl_items...\n";
    if (!Schema::hasTable('tanda_terima_lcl_items')) {
        echo "   ✗ Tabel tanda_terima_lcl_items TIDAK ADA, membuat tabel...\n";
        try {
            DB::statement("
                CREATE TABLE `tanda_terima_lcl_items` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `tanda_terima_lcl_id` bigint unsigned NOT NULL,
                    `item_number` int NOT NULL DEFAULT '1',
                    `nama_barang` varchar(255) DEFAULT NULL,
                    `keterangan_barang` text,
                    `jumlah` int DEFAULT NULL,
                    `satuan` varchar(50) DEFAULT NULL,
                    `panjang` decimal(10,3) DEFAULT NULL COMMENT 'Length in meters',
                    `lebar` decimal(10,3) DEFAULT NULL COMMENT 'Width in meters',
                    `tinggi` decimal(10,3) DEFAULT NULL COMMENT 'Height in meters',
                    `meter_kubik` decimal(12,3) DEFAULT NULL COMMENT 'Volume in m³',
                    `tonase` decimal(10,3) DEFAULT NULL COMMENT 'Weight in tons',
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `tanda_terima_lcl_items_tanda_terima_lcl_id_foreign` (`tanda_terima_lcl_id`),
                    CONSTRAINT `tanda_terima_lcl_items_tanda_terima_lcl_id_foreign` 
                        FOREIGN KEY (`tanda_terima_lcl_id`) 
                        REFERENCES `tanda_terimas_lcl` (`id`) 
                        ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "   ✓ Tabel tanda_terima_lcl_items berhasil dibuat\n";
        } catch (\Exception $e) {
            echo "   ✗ Gagal membuat tabel: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✓ Tabel tanda_terima_lcl_items sudah ada\n";
        
        // Cek kolom item_number, jumlah, satuan
        if (!Schema::hasColumn('tanda_terima_lcl_items', 'item_number')) {
            try {
                DB::statement("ALTER TABLE tanda_terima_lcl_items ADD COLUMN item_number INT NOT NULL DEFAULT 1 AFTER tanda_terima_lcl_id");
                echo "   ✓ Kolom item_number ditambahkan\n";
            } catch (\Exception $e) {
                echo "   ✗ Gagal tambah item_number: " . $e->getMessage() . "\n";
            }
        }
        
        // Rename jumlah_koli ke jumlah jika ada
        if (Schema::hasColumn('tanda_terima_lcl_items', 'jumlah_koli') && 
            !Schema::hasColumn('tanda_terima_lcl_items', 'jumlah')) {
            try {
                DB::statement("ALTER TABLE tanda_terima_lcl_items CHANGE COLUMN jumlah_koli jumlah INT NULL");
                echo "   ✓ Kolom jumlah_koli di-rename ke jumlah\n";
            } catch (\Exception $e) {
                echo "   ✗ Gagal rename jumlah_koli: " . $e->getMessage() . "\n";
            }
        }
        
        if (!Schema::hasColumn('tanda_terima_lcl_items', 'satuan')) {
            try {
                DB::statement("ALTER TABLE tanda_terima_lcl_items ADD COLUMN satuan VARCHAR(50) NULL AFTER jumlah");
                echo "   ✓ Kolom satuan ditambahkan\n";
            } catch (\Exception $e) {
                echo "   ✗ Gagal tambah satuan: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\n";

    // 6. Verifikasi tabel pivot
    echo "5. Verifikasi tabel tanda_terima_lcl_kontainer_pivot...\n";
    if (!Schema::hasTable('tanda_terima_lcl_kontainer_pivot')) {
        echo "   ✗ Tabel tanda_terima_lcl_kontainer_pivot TIDAK ADA, membuat tabel...\n";
        try {
            DB::statement("
                CREATE TABLE `tanda_terima_lcl_kontainer_pivot` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `tanda_terima_lcl_id` bigint unsigned NOT NULL,
                    `nomor_kontainer` varchar(255) DEFAULT NULL,
                    `size_kontainer` varchar(255) DEFAULT NULL,
                    `tipe_kontainer` varchar(255) DEFAULT NULL,
                    `nomor_seal` varchar(255) DEFAULT NULL,
                    `tanggal_seal` date DEFAULT NULL,
                    `assigned_at` timestamp NULL DEFAULT NULL,
                    `assigned_by` bigint unsigned DEFAULT NULL,
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `tanda_terima_lcl_kontainer_pivot_tanda_terima_lcl_id_foreign` (`tanda_terima_lcl_id`),
                    KEY `tanda_terima_lcl_kontainer_pivot_assigned_by_foreign` (`assigned_by`),
                    KEY `idx_lcl_pivot_kontainer` (`nomor_kontainer`),
                    KEY `idx_lcl_pivot_kontainer_tt` (`nomor_kontainer`,`tanda_terima_lcl_id`),
                    CONSTRAINT `tanda_terima_lcl_kontainer_pivot_tanda_terima_lcl_id_foreign` 
                        FOREIGN KEY (`tanda_terima_lcl_id`) 
                        REFERENCES `tanda_terimas_lcl` (`id`) 
                        ON DELETE CASCADE,
                    CONSTRAINT `tanda_terima_lcl_kontainer_pivot_assigned_by_foreign` 
                        FOREIGN KEY (`assigned_by`) 
                        REFERENCES `users` (`id`) 
                        ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "   ✓ Tabel tanda_terima_lcl_kontainer_pivot berhasil dibuat\n";
        } catch (\Exception $e) {
            echo "   ✗ Gagal membuat tabel: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✓ Tabel tanda_terima_lcl_kontainer_pivot ada\n";
    }
    echo "\n";

    // 7. Summary
    echo "==============================================\n";
    echo "SELESAI!\n";
    echo "==============================================\n";
    echo "Langkah selanjutnya:\n";
    echo "1. Jalankan: php artisan migrate\n";
    echo "2. Jika masih error, cek log di atas\n";
    echo "3. Test fitur stuffing & seal di aplikasi\n";
    echo "\n";

} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
