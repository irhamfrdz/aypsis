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
        '2025_12_15_090852_create_tanda_terima_lcl_tables',
        '2025_12_15_091741_remove_unused_columns_from_tanda_terimas_lcl_table',
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

    // 4. Verifikasi tabel pivot
    echo "4. Verifikasi tabel tanda_terima_lcl_kontainer_pivot...\n";
    if (Schema::hasTable('tanda_terima_lcl_kontainer_pivot')) {
        echo "   ✓ Tabel tanda_terima_lcl_kontainer_pivot ada\n";
    } else {
        echo "   ✗ Tabel tanda_terima_lcl_kontainer_pivot TIDAK ADA\n";
        echo "   → Perlu jalankan: php artisan migrate\n";
    }
    echo "\n";

    // 5. Summary
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
