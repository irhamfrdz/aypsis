<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check indexes on manifests table
$indexes = DB::select('SHOW INDEXES FROM manifests');
$columns = Schema::getColumnListing('manifests');

echo "Columns in manifests table:\n";
print_r($columns);

echo "\nIndexes on manifests table:\n";
foreach ($indexes as $index) {
    echo "Key_name: " . $index->Key_name . 
         ", Column_name: " . $index->Column_name . 
         ", Non_unique: " . $index->Non_unique . "\n";
}
