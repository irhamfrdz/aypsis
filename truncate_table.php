<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Truncate Table ===\n\n";

$count = DB::table('daftar_tagihan_kontainer_sewa')->count();
echo "Current records: $count\n";

DB::table('daftar_tagihan_kontainer_sewa')->truncate();

echo "✓ Table truncated!\n";

$afterCount = DB::table('daftar_tagihan_kontainer_sewa')->count();
echo "Records after truncate: $afterCount\n";

echo "\n=== Selesai ===\n";
