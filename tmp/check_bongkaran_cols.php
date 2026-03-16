<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$cols = Schema::getColumnListing('surat_jalan_bongkarans');
echo "Kolom surat_jalan_bongkarans: " . implode(', ', $cols) . "\n";

// Cek sample data yang mengandung nur/cece
$rows = DB::table('surat_jalan_bongkarans')
    ->whereRaw("LOWER(supir) LIKE '%nur%' OR LOWER(supir) LIKE '%cece%'")
    ->select('id', 'supir')
    ->limit(10)
    ->get();
echo "Sample data nur/cece di surat_jalan_bongkarans:\n";
foreach ($rows as $r) {
    echo "  id={$r->id} supir='{$r->supir}'\n";
}
echo "Total: " . count($rows) . " baris.\n";
