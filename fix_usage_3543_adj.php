<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Target: 35.595.000
// Current: 29.662.500
// Adjustment: 35.595.000 - 29.662.500 = 5.932.500
\App\Models\StockAmprahanUsage::where('id', 3543)->update(['adjustment_nilai_keluar' => 5932500]);

echo "Usage 3543 Adjustment Updated to 5932500!\n";
