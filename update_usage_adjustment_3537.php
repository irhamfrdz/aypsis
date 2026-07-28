<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahanUsage::where('id', 3537)->update(['adjustment_nilai_keluar' => 52537500]);

echo "Usage 3537 Adjustment Updated to 52537500!\n";
