<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 4088)->update(['adjustment_nilai_keluar' => 14010000]);

echo "Usage 4088 Adjustment Updated to 14010000!\n";
