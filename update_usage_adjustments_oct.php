<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3530)->update(['adjustment_nilai_keluar' => 1880979]);
\App\Models\StockAmprahanUsage::where('id', 3531)->update(['adjustment_nilai_keluar' => 50656521]);

echo "Usage adjustments updated!\n";
