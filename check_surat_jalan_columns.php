<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ CEK STRUKTUR TABEL SURAT_JALANS                                              ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Cek kolom di tabel surat_jalans
$columns = DB::select("SHOW COLUMNS FROM surat_jalans");

echo "📋 KOLOM DI TABEL SURAT_JALANS:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
foreach ($columns as $column) {
    echo $column->Field . " (" . $column->Type . ")\n";
}
echo "────────────────────────────────────────────────────────────────────────────────\n\n";

// Cari data yang nomornya mirip SJ00006
echo "📋 CARI SURAT JALAN DENGAN NOMOR MIRIP 'SJ00006':\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$results = DB::table('surat_jalans')
    ->select('id', 'created_at')
    ->get();

echo "Total surat jalan: " . $results->count() . "\n\n";

foreach ($results as $sj) {
    echo "ID: " . $sj->id . " | Created: " . $sj->created_at . "\n";
}

echo "────────────────────────────────────────────────────────────────────────────────\n";
