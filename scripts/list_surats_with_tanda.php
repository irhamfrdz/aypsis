<?php
// Script: list_surats_with_tanda.php
// Purpose: List distinct surat jalan numbers that have Tanda Terima

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the console kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TandaTerima;

// Query distinct surat jalan numbers that have tanda terima (non-null surat_jalan_id)
$results = DB::table('tanda_terimas')
    ->join('surat_jalans', 'surat_jalans.id', '=', 'tanda_terimas.surat_jalan_id')
    ->select('surat_jalans.no_surat_jalan')
    ->distinct()
    ->orderBy('surat_jalans.no_surat_jalan')
    ->pluck('surat_jalans.no_surat_jalan')
    ->toArray();

if (empty($results)) {
    echo "No surat jalan found that have Tanda Terima.\n";
    exit(0);
}

echo "Surat Jalan that have Tanda Terima:\n";
foreach ($results as $no) {
    echo "- " . $no . "\n";
}

echo "\nTotal: " . count($results) . "\n";
