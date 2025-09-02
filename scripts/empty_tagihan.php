<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function safeCount($table) {
    if (!Schema::hasTable($table)) return null;
    return DB::table($table)->count();
}

$tables = ['tagihan_kontainer_sewa_kontainers', 'tagihan_kontainer_sewa'];

foreach ($tables as $t) {
    $c = safeCount($t);
    if ($c === null) {
        echo "TABLE_MISSING: $t\n";
    } else {
        echo "BEFORE_COUNT $t: $c\n";
    }
}

// Disable FK checks where supported
try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
} catch (\Exception $e) {
    // ignore for sqlite or unsupported engines
}

// Delete data if table exists
foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        DB::table($t)->delete();
        echo "DELETED_ALL_ROWS_FROM: $t\n";
    }
}

// Re-enable FK checks
try {
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
} catch (\Exception $e) {
    // ignore
}

foreach ($tables as $t) {
    $c = safeCount($t);
    if ($c === null) {
        echo "TABLE_MISSING_AFTER: $t\n";
    } else {
        echo "AFTER_COUNT $t: $c\n";
    }
}

echo "DONE\n";
