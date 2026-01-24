<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;
$cols = Schema::getColumnListing('prospeks');
file_put_contents('prospeks_cols.txt', implode(',', $cols));
echo "Done\n";
