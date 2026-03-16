<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('surat_jalans')
    ->whereRaw("LOWER(supir) LIKE '%nur%' OR LOWER(supir) LIKE '%cece%' OR LOWER(supir2) LIKE '%nur%' OR LOWER(supir2) LIKE '%cece%'")
    ->select('id', 'supir', 'supir2')
    ->limit(30)
    ->get();

foreach ($rows as $r) {
    echo "id={$r->id} supir='{$r->supir}' supir2='{$r->supir2}'\n";
}

echo "Total: " . count($rows) . " baris.\n";
