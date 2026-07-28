<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3532)->update(['adjustment_nilai_keluar' => 17512500]);

echo "Usage 3532 Adjustment Updated to 17512500!\n";
