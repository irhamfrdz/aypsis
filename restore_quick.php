<?php
// restore_quick.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Restoring from backup_bulatkan_20251111_170626...\n";
DB::statement('TRUNCATE TABLE daftar_tagihan_kontainer_sewa');
DB::statement('INSERT INTO daftar_tagihan_kontainer_sewa SELECT * FROM daftar_tagihan_kontainer_sewa_backup_bulatkan_20251111_170626');
echo "Restored. Total: " . DB::table('daftar_tagihan_kontainer_sewa')->count() . "\n";
