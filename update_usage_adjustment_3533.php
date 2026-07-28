<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3533)->update(['adjustment_nilai_keluar' => 17512500]);

echo "Usage 3533 Adjustment Updated to 17512500!\n";
