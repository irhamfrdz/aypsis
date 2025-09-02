<?php
// Clear daftar_tagihan_kontainer_sewas table safely via Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

// determine table name from model
$table = (new DaftarTagihanKontainerSewa())->getTable();
try {
    $before = DB::table($table)->count();
} catch (\Throwable $e) {
    echo "Table does not exist: $table\n";
    exit(1);
}

// Use truncate if supported, otherwise delete
try {
    DaftarTagihanKontainerSewa::truncate();
    $method = 'truncate';
} catch (\Throwable $e) {
    DB::table($table)->delete();
    $method = 'delete';
}
try {
    $after = DB::table($table)->count();
} catch (\Throwable $e) {
    $after = 0;
}

echo "Cleared table $table using: $method\n";
echo "Before: $before rows\n";
echo "After: $after rows\n";
