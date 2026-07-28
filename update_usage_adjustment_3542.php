<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3542)->update(['adjustment_nilai_keluar' => 19775000]);

echo "Usage 3542 Adjustment Updated to 19775000!\n";
