<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$num = 'AYPU0173472';
echo "Searching for $num in naik_kapal:\n";
$nk = DB::table('naik_kapal')->where('nomor_kontainer', $num)->orderBy('id', 'desc')->first();
if ($nk) {
    echo "Last NaikKapal ID: " . $nk->id . ", Prospek ID: " . $nk->prospek_id . "\n";
} else {
    echo "Not found in naik_kapal\n";
}

echo "\nSearching for $num in bls:\n";
$bl = DB::table('bls')->where('nomor_kontainer', $num)->orderBy('id', 'desc')->first();
if ($bl) {
    echo "Last BL ID: " . $bl->id . ", Prospek ID: " . $bl->prospek_id . "\n";
} else {
    echo "Not found in bls\n";
}

echo "\nChecking prospeks with ID 0 or NULL:\n";
$p0 = DB::table('prospek')->where('id', 0)->first();
if ($p0) echo "Prospek ID 0 exists\n"; else echo "Prospek ID 0 does NOT exist\n";
