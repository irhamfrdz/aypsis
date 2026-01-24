<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$voyage = 'SA01PJ26';

$manifestCount = DB::table('manifests')->where('no_voyage', $voyage)->count();
$sjbCount = DB::table('surat_jalan_bongkarans')->where('no_voyage', $voyage)->count();

echo "Counts for voyage $voyage:\n";
echo "Manifests: $manifestCount\n";
echo "Surat Jalan Bongkarans: $sjbCount\n";
