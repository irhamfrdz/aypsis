<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$cols = Schema::getColumnListing('tanda_terimas_lcl');
echo "Columns in tanda_terimas_lcl:\n";
print_r($cols);
