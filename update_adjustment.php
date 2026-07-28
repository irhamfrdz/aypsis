<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahan::where('id', 3776)->update(['adjustment' => 336240000]);
\App\Models\StockAmprahan::where('id', 3777)->update(['adjustment' => 3657500]);
\App\Models\StockAmprahan::where('id', 3781)->update(['adjustment' => 197750000]);

echo "Adjustment Updated!";
