<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3535)->update(['adjustment_nilai_keluar' => 105075000]);

echo "Usage 3535 Adjustment Updated to 105075000!\n";
