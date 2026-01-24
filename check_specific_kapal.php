<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking for 'KM. SUMBER ABADI' in bls table:\n";

// Exact match check
$exact = DB::table('bls')->where('nama_kapal', 'KM. SUMBER ABADI')->count();
echo "- Exact match 'KM. SUMBER ABADI': $exact records\n";

// Like match check
$like = DB::table('bls')->where('nama_kapal', 'like', '%KM. SUMBER ABADI%')->get();
echo "- Like match '%KM. SUMBER ABADI%': " . $like->count() . " records\n";

if ($like->count() > 0) {
    echo "\nVariations found:\n";
    foreach($like->groupBy('nama_kapal') as $name => $group) {
        echo "- '$name' : {$group->count()} records\n";
    }
}
