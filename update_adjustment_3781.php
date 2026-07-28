<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahan::where('id', 3781)->update(['adjustment' => 647885000]);

echo "Adjustment for 3781 Updated!";
