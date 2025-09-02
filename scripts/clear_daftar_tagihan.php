<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$table = 'daftar_tagihan_kontainer_sewa';
try {
    $before = DB::table($table)->count();
    echo "Rows before: $before\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table($table)->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    $after = DB::table($table)->count();
    echo "Rows after: $after\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
