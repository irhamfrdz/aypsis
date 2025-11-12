<?php
// restore_from_backup.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Restoring from backup table...\n";

DB::statement('TRUNCATE TABLE daftar_tagihan_kontainer_sewa');
DB::statement('INSERT INTO daftar_tagihan_kontainer_sewa SELECT * FROM daftar_tagihan_kontainer_sewa_backup_20251111_162908');

$total = DB::table('daftar_tagihan_kontainer_sewa')->count();
echo "Restored successfully. Total records: {$total}\n";

// Verify MSKU2218091 P4
$item = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'MSKU2218091')
    ->where('periode', 4)
    ->first();

if ($item) {
    echo "\nVerification - MSKU2218091 P4:\n";
    echo "DPP: {$item->dpp}\n";
    echo "Tanggal: {$item->tanggal_awal} to {$item->tanggal_akhir}\n";
}
