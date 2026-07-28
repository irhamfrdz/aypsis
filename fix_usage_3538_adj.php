<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3538)->update(['adjustment_nilai_keluar' => -200000]);

echo "Usage 3538 Adjustment Updated to -200000!\n";
