<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;

if (Schema::hasTable('surat_jalan_bongkarans')) {
    echo "Columns for surat_jalan_bongkarans:\n";
    print_r(Schema::getColumnListing('surat_jalan_bongkarans'));
} else {
    echo "Table surat_jalan_bongkarans does not exist.\n";
}
