<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\PricelistCat;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Memulai pembersihan database pricelist_cat...\n";

try {
    // Hitung jumlah record sebelum pembersihan
    $countBefore = PricelistCat::count();
    echo "Jumlah record sebelum pembersihan: {$countBefore}\n";

    // Truncate tabel pricelist_cat
    PricelistCat::truncate();

    // Hitung jumlah record setelah pembersihan
    $countAfter = PricelistCat::count();
    echo "Jumlah record setelah pembersihan: {$countAfter}\n";

    echo "✅ Database pricelist_cat berhasil dibersihkan!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Proses selesai.\n";
