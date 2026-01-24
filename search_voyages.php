<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$search = 'SA01PJ26';

$manifests = DB::table('manifests')
    ->where('no_voyage', 'LIKE', "%$search%")
    ->select('no_voyage')
    ->distinct()
    ->get();

echo "Similar voyages in manifests:\n";
print_r($manifests);

$sjb = DB::table('surat_jalan_bongkarans')
    ->where('no_voyage', 'LIKE', "%$search%")
    ->select('no_voyage')
    ->distinct()
    ->get();

echo "Similar voyages in SJB:\n";
print_r($sjb);
