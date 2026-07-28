<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3534)->update(['adjustment_nilai_keluar' => 63045000]);

echo "Usage 3534 Adjustment Updated to 63045000!\n";
