<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$table = 'master_pricelist_biaya_storages';
$columns = Schema::getColumnListing($table);
echo "Columns in $table:\n";
print_r($columns);
