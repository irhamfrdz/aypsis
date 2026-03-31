<?php
/**
 * Script Reset BTM Sewa Tables - Production
 * 
 * Jalankan script ini SEBELUM php artisan migrate
 * pada server production setelah git pull.
 * 
 * Cara pakai:
 *   php reset_btm_sewa.php
 *   php artisan migrate
 * 
 * Script ini akan:
 * 1. Drop semua tabel btm_sewa_* yang lama
 * 2. Hapus record migrasi terkait dari tabel migrations
 * 3. Setelah itu, jalankan php artisan migrate untuk membuat ulang tabel dengan skema baru
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "============================================\n";
echo "  RESET BTM SEWA TABLES - PRODUCTION\n";
echo "============================================\n\n";

// Step 1: Disable foreign key checks
echo "[1/3] Menonaktifkan foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Step 2: Drop all btm_sewa tables (urutan: child tables dulu)
echo "[2/3] Menghapus tabel btm_sewa...\n";
$tables = [
    'btm_sewa_audits',
    'btm_sewa_pranotas',
    'btm_sewa_transactions',
    'btm_sewa_rates',
    'btm_sewa_units',
    'btm_sewa_sizes',
    'btm_sewa_types',
    'btm_sewa_vendors',
];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        Schema::drop($table);
        echo "  ✓ Dropped: {$table}\n";
    } else {
        echo "  - Skipped (tidak ada): {$table}\n";
    }
}

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');

// Step 3: Clean migration records
echo "[3/3] Membersihkan record migrasi...\n";
$migrations = [
    '2026_03_17_130000_create_btm_kontainer_sewa_tables',
    '2026_03_28_000000_create_btm_sewa_pranotas_table',
    '2026_03_28_100835_create_btm_sewa_audits_table',
    '2026_03_31_132140_add_note_to_btm_sewa_audits_table',
    '2026_03_31_132908_create_btm_sewa_pranotas_table',
    '2026_03_31_132925_add_pranota_id_to_btm_sewa_audits_table',
    '2026_03_31_133805_create_btm_sewa_pranotas_table',
];

$deleted = DB::table('migrations')->whereIn('migration', $migrations)->delete();
echo "  ✓ Dihapus: {$deleted} record migrasi\n";

echo "\n============================================\n";
echo "  SELESAI! Sekarang jalankan:\n";
echo "  php artisan migrate\n";
echo "============================================\n";
