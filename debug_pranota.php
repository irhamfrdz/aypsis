<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pranota;
use App\Models\TagihanCat;

$pranota = Pranota::find(2);
echo "tagihan_ids: " . json_encode($pranota->tagihan_ids) . "\n";
echo "Max tagihan_cat id: " . TagihanCat::max('id') . "\n";
echo "Count tagihan_cat: " . TagihanCat::count() . "\n";

if (is_array($pranota->tagihan_ids)) {
    foreach ($pranota->tagihan_ids as $id) {
        $exists = TagihanCat::where('id', $id)->exists();
        echo "ID $id exists: " . ($exists ? 'YES' : 'NO') . "\n";
    }
}
