<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== Columns in 'prospek' table ===\n";
print_r(Schema::getColumnListing('prospek'));

echo "\n=== Columns in 'tanda_terima' table ===\n";
print_r(Schema::getColumnListing('tanda_terima'));

echo "\n=== Columns in 'tanda_terima_tanpa_surat_jalan' table ===\n";
print_r(Schema::getColumnListing('tanda_terima_tanpa_surat_jalan'));
