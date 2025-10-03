<?php
/**
 * Script untuk memperbaiki masalah migrasi stock_kontainers
 * Jalankan script ini di server untuk membersihkan masalah migrasi
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STOCK KONTAINERS MIGRATION FIX ===\n\n";

try {
    // Periksa apakah tabel stock_kontainers ada
    if (!Schema::hasTable('stock_kontainers')) {
        echo "❌ Tabel stock_kontainers tidak ditemukan!\n";
        echo "✅ Silakan jalankan migrasi create table terlebih dahulu.\n";
        exit(1);
    }

    echo "✅ Tabel stock_kontainers ditemukan.\n\n";

    // Periksa kolom yang ada
    echo "📋 Memeriksa kolom yang ada di tabel stock_kontainers:\n";
    $columns = Schema::getColumnListing('stock_kontainers');
    
    foreach ($columns as $column) {
        echo "  ✓ {$column}\n";
    }
    echo "\n";

    // Periksa kolom yang bermasalah
    $problematicColumns = ['kondisi', 'lokasi', 'harga_sewa_per_hari', 'harga_sewa_per_bulan', 'pemilik'];
    $existingProblematicColumns = [];
    
    echo "🔍 Memeriksa kolom yang akan dihapus:\n";
    foreach ($problematicColumns as $column) {
        if (Schema::hasColumn('stock_kontainers', $column)) {
            echo "  ⚠️  {$column} - ADA (akan dihapus)\n";
            $existingProblematicColumns[] = $column;
        } else {
            echo "  ✅ {$column} - TIDAK ADA (aman)\n";
        }
    }
    echo "\n";

    // Periksa status migrasi yang bermasalah
    echo "📊 Memeriksa status migrasi:\n";
    $migrations = DB::table('migrations')
        ->where('migration', 'LIKE', '%stock_kontainers%')
        ->get();

    foreach ($migrations as $migration) {
        echo "  📁 {$migration->migration} - Batch: {$migration->batch}\n";
    }
    echo "\n";

    // Jika ada kolom yang bermasalah dan migrasi sudah tercatat, hapus dari migrations table
    $problematicMigrations = [
        '2025_10_01_131657_remove_unused_columns_from_stock_kontainers_table',
        '2025_10_01_144516_remove_kondisi_and_lokasi_from_stock_kontainers_table'
    ];

    $foundProblematicMigrations = DB::table('migrations')
        ->whereIn('migration', $problematicMigrations)
        ->get();

    if ($foundProblematicMigrations->count() > 0) {
        echo "⚠️  Ditemukan migrasi bermasalah yang sudah tercatat:\n";
        foreach ($foundProblematicMigrations as $migration) {
            echo "  🔄 {$migration->migration}\n";
        }
        
        echo "\n🔧 Membersihkan record migrasi bermasalah...\n";
        $deleted = DB::table('migrations')
            ->whereIn('migration', $problematicMigrations)
            ->delete();
        
        echo "✅ Berhasil menghapus {$deleted} record migrasi bermasalah.\n\n";
    }

    // Manual cleanup jika kolom masih ada
    if (!empty($existingProblematicColumns)) {
        echo "🔧 Membersihkan kolom yang bermasalah secara manual...\n";
        
        foreach ($existingProblematicColumns as $column) {
            try {
                DB::statement("ALTER TABLE stock_kontainers DROP COLUMN IF EXISTS `{$column}`");
                echo "  ✅ Kolom {$column} berhasil dihapus.\n";
            } catch (Exception $e) {
                echo "  ⚠️  Gagal menghapus kolom {$column}: " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }

    echo "🎉 SELESAI!\n";
    echo "📝 Sekarang Anda dapat menjalankan migrasi dengan aman:\n";
    echo "   php artisan migrate\n\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📝 Stack trace:\n" . $e->getTraceAsString() . "\n";
}