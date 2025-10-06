<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MasterPricelistSewaKontainer;

echo "=== MASTER PRICELIST SEWA KONTAINER ===\n";

$pricelists = MasterPricelistSewaKontainer::all();

foreach ($pricelists as $pricelist) {
    echo sprintf(
        "%s %sft: %s (%s)\n",
        $pricelist->vendor,
        $pricelist->ukuran_kontainer,
        number_format($pricelist->harga),
        $pricelist->tarif
    );
}

?>
