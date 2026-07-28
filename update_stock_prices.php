<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\StockAmprahan::where('id', 3776)->update(['harga_satuan' => 9937500]);
\App\Models\StockAmprahan::where('id', 3777)->update(['harga_satuan' => 9887500]);
\App\Models\StockAmprahan::where('id', 3781)->update(['harga_satuan' => 9887500]);

echo "Updated!";
