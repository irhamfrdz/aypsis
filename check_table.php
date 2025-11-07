<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('uang_jalans');
echo "Columns in uang_jalans table:\n";
foreach($columns as $column) {
    echo "- " . $column . "\n";
}